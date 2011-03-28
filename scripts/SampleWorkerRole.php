<?php
use SE\Tweet\Classifier;
ini_set('html_errors', 0);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');
$em = $doctrine->getEntityManager();
// ----------------------- PICK UP JOB --------------- //

$location = array('location' => 'http://sentiment-engine.dev/api/tracking-fulfillment');

$client = new SE\Infrastructure\Tracking\APIJobClient($location, new \Zend_Http_Client());

$job = $client->getJob();

$term = cleanString($job['content']['trackingitem']['term']);

$jobTerm = $em->find('SE\Entity\TrackingItem', $job['content']['trackingitem']['id']);

// ----------------------- DO JOB      ----------------//

// ----------------------- Gather Initial sample ------// 


// Gather a sample of Tweets with the keyword in.
$sampler = new SE\Tweet\Utility\Sampler(new \Zend_Http_Client(), $term, 1500);
$sample = $sampler->gatherSample();

// Sample is pre sorted (array has two p / n for postive and negative tweets.
// ------ Create a new Classification Set
$classificationSet = new SE\Entity\ClassificationSet();
$classificationSet->setType(\SE\Entity\ClassificationSet::TYPE_CORPUS);
$classificationSet->setDate(new DateTime());
$classificationSet->setSampleSize(1700);
$classificationSet->setTerm($jobTerm);
$em->persist($classificationSet);

// Give Tweets a classification and add them to the classification set

$count = 1;

foreach ($sample['p'] as $tweetData)
{
    $tweet = jsonToTweet($tweetData);
    cleanTweet($tweet);

    $classification = new SE\Entity\TweetClassification($tweet);
    $classification->setClassificationType(1);
    $classification->setClassificationResult(SE\Tweet\Classifier\Classifier::CLASSIFICATION_RESULT_POSITIVE);
    $classification->setClassificationTime(new \DateTime());

    $tweet->addClassification($classification);
    $classificationSet->addTweet($classification);

    $em->persist($tweet);
    $em->persist($classification);

    echo $count++ . "\n";
}


foreach ($sample['n'] as $tweetData)
{
    $tweet = jsonToTweet($tweetData);
    cleanTweet($tweet);

    $classification = new SE\Entity\TweetClassification($tweet);
    $classification->setClassificationType(1);
    $classification->setClassificationResult(SE\Tweet\Classifier\Classifier::CLASSIFICATION_RESULT_NEGATIVE);
    $classification->setClassificationTime(new \DateTime());

    $tweet->addClassification($classification);
    $classificationSet->addTweet($classification);

    $em->persist($tweet);
    $em->persist($classification);
    echo $count++ . "\n";
}

$em->flush();


// ------------------- Create Bayes Sample Data

$conn = $em->getConnection();

$wordStorage = new SE\Tweet\Utility\WordStorage($conn);
// -- @todo Change this Query to pick the correct Classification set al the time
$query = $em->createQuery('SELECT c, tc FROM SE\Entity\ClassificationSet c JOIN c.classifiedTweets tc');
$sets = $query->getResult();
$set = $sets[0];

$count = 1;

// Prepare the most common wrds array

$h = fopen('commonwords.txt', 'r');
$cWordAr = array();
$inc = 0;
while(($word = fgets($h)) !== FALSE)
{
   $cWordAr[] = trim($word);
   $inc++;
   if($inc >= 200) break;
}

fclose($h);



// Get all the tweets in a classification set.
foreach ($set->getTweets() as $classification)
{
    echo 'Tweet: ' . $count++ . "  ";
    $tweet  = $classification->getTweet();
    $text   = $tweet->getText();
    $wordAr = explode(' ', $text);

    $wordsInTweet = array();

    // Explode the words in a tweet and check for the presence of the word in the set using a DQL query.
    foreach ($wordAr as $word)
    {

        if($word == " " || $word == "" || in_array($word, $wordsInTweet) || in_array($word, $cWordAr) || $word == $term)
        {
            // Words are only counted once. A double space is not a word
            continue;
        }

        $wordsInTweet[] = $word;

        $wordAr = $wordStorage->getWord($set->getId(), $word);

        if(is_null($wordAr) || !$wordAr)
        {
            echo 'N ';
            $result = $classification->getClassificationResult();

            // Insert the word into the database
            $wA = array('classification_set_word_set_id' => $set->getId(),
                        'classification_set_word_word' => $word,
                        'classification_set_word_positive' => ($result == Classifier\Classifier::CLASSIFICATION_RESULT_POSITIVE) ? 1: 0,
                        'classification_set_word_negative' => ($result == Classifier\Classifier::CLASSIFICATION_RESULT_NEGATIVE) ? 1 : 0,
                        'classification_set_word_appearences' => 1
                        );

            $wordStorage->insertWord($wA);
        }
        else
        {
            echo 'U ';
            // Update the entry in the database
            $result = $classification->getClassificationResult();

            if($result == Classifier\Classifier::CLASSIFICATION_RESULT_POSITIVE)
            {
                $wordAr['classification_set_word_positive']++;
            }
            else
            {
                $wordAr['classification_set_word_negative']++;
            }

            $wordAr['classification_set_word_appearences']++;

            $wordStorage->updateWord($wordAr);
        }
    }

    echo "\n";
}

// ------------------------- Job Complete
$client->registerCompleteJob($job);

// ---------------------------------------------- Utility Functions
// -- @todo refactor into a class based solution
// Convert JSON entity into a tweet object
function jsonToTweet($data)
{
    $tweet = new SE\Entity\Tweet();
    $tweet->setText($data['text']);
    $tweet->setTweetId($data['id_str']);
    $tweet->setDate(DateTime::createFromFormat('D, d M Y H:i:s O', $data['created_at']));
    $tweet->setUser($data['from_user']);
    $tweet->setLanguage($data['iso_language_code']);

    return $tweet;
}

// Clean up a Tweet before it is persisted
function cleanTweet($tweet)
{
    $text = $tweet->getText();
    $words = explode(' ', $text);

    foreach ($words as $key => &$word)
    {
        if (strpos($word, '@') === 0)
        {
            unset($words[$key]);
        }
        elseif ($word == ' ')
        {
            unset($words[$key]);
        }
        else
        {
            $word = cleanString($word);
        }
    }
    $tweet->setText(implode(' ', $words));
}

function cleanString($string)
{
    $string = str_replace(array(':)', ':-)', ':p', ':(', ':-(', ':', ',', ';', '.', "\'", 'RT', '!', "\""), '', $string);
    $string = mb_strtolower($string, 'UTF8');

    return $string;
}

