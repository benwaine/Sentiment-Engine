<?php

use SE\Tweet\Classifier;

ini_set('html_errors', 0);
ini_set('display_errors', 1);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');
$em = $doctrine->getEntityManager();


// ----------------------- PICK UP JOB --------------- //

$opts = $bootstrap->getOptions();

$location = array('location' => $opts['api']['sample']);

$client = new SE\Infrastructure\Tracking\APIJobClient($location, new \Zend_Http_Client());

while (true)
{

    $job = $client->getJob();

    if(!$job)
    {
        echo "No Job. Sleep / Re-Request. \n";
        sleep(10);
        continue;
    }

    $term = cleanString($job['content']['trackingitem']['term']);

    echo "Begining Sampling For Term: $term \n";
   
    $jobTerm = $em->find('SE\Entity\TrackingItem', $job['content']['trackingitem']['id']);

// ----------------------- DO JOB      ----------------//
// ----------------------- Gather Initial sample ------//
// Gather a sample of Tweets with the keyword in.
    $sampler = new SE\Tweet\Utility\Sampler(new \Zend_Http_Client(), $term, 1000);
    $sample = $sampler->gatherSample();

// Sample is pre sorted (array has two p / n for postive and negative tweets.
// ------ Create a new Classification Set
    $classificationSet = new SE\Entity\ClassificationSet();
    $classificationSet->setType(\SE\Entity\ClassificationSet::TYPE_CORPUS);
    $classificationSet->setDate(new DateTime());

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
    
    $query = $em->createQuery('SELECT c, tc FROM SE\Entity\ClassificationSet c JOIN c.classifiedTweets tc WHERE c.term = ?1');
    $query->setParameter('1', $job['content']['trackingitem']['id']);
    $sets = $query->getResult();
    

    if(!isset($sets))
    {
        $client->resetJob($job);
    }
    
    $set = $sets[0];

    $count = 1;

// Prepare the most common wrds array

    $h = fopen('commonwords.txt', 'r');
    $cWordAr = array();
    $inc = 0;
    while (($word = fgets($h)) !== FALSE)
    {
        $cWordAr[] = trim($word);
        $inc++;

        if ($inc >= 400)

            break;
    }

    fclose($h);

    $allWordsAr = array();

    // Get all the tweets in a classification set.
    foreach ($set->getTweets() as $classification)
    {
        echo 'Tweet: ' . $count++ . "  ";
        $tweet = $classification->getTweet();
        $text = $tweet->getText();
        $wordAr = explode(' ', $text);

        $wordsInTweet = array();

        foreach ($wordAr as $word)
        {

            if ($word == " " || $word == "" || in_array($word, $wordsInTweet) || in_array($word, $cWordAr) || $word == $term)
            {
                // Words are only counted once. A double space is not a word
                continue;
            }

            $wordsInTweet[] = $word;

            //$wordAr = $wordStorage->getWord($set->getId(), $word);

            if (!array_key_exists($word, $allWordsAr))
            {
                echo 'N ';
                $result = $classification->getClassificationResult();

                // Insert the word into the database
                $wA = array('classification_set_word_set_id' => $set->getId(),
                    'classification_set_word_word' => $word,
                    'classification_set_word_positive' => ($result == Classifier\Classifier::CLASSIFICATION_RESULT_POSITIVE) ? 1 : 0,
                    'classification_set_word_negative' => ($result == Classifier\Classifier::CLASSIFICATION_RESULT_NEGATIVE) ? 1 : 0,
                    'classification_set_word_appearences' => 1
                );

                $allWordsAr[$word] = $wA;
            }
            else
            {
                // Update the word entry in the Array.
                echo 'U ';

                $result = $classification->getClassificationResult();

                if ($result == Classifier\Classifier::CLASSIFICATION_RESULT_POSITIVE)
                {
                    $allWordsAr[$word]['classification_set_word_positive']++;
                    echo $allWordsAr[$word]['classification_set_word_positive'];
                }
                else
                {
                    $allWordsAr[$word]['classification_set_word_negative']++;
                }

                $allWordsAr[$word]['classification_set_word_appearences']++;

            }
        }
        echo "\n";
    }
    $wordStorage->insertWordArray($allWordsAr);

// ------------------------- Job Complete
    $client->registerCompleteJob($job);
}

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
    $string = str_replace(array(':p', ':-p'), '', $string);
    $string = str_replace(array(':)', ':-)', ':p', ':(', ':-(', ':', ',', ';', '.', "\'", 'RT', '!', "\"", "?" ), '', $string);

    $string = mb_strtolower($string, 'UTF8');

    return $string;
}

