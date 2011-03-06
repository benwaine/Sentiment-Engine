<?php

ini_set('html_errors', 0);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');

$em = $doctrine->getEntityManager();

// Gather a sample of Tweets with the keyword in.
$sampler = new SE\Tweet\Utility\Sampler(new \Zend_Http_Client(), 'birthday', 750);
$sample = $sampler->gatherSample();

// Sample is pre sorted (array has two p / n for postive and negative tweets.
// ------ Create a new Classification Set
$classificationSet = new SE\Entity\ClassificationSet();
$classificationSet->setType(\SE\Entity\ClassificationSet::TYPE_CORPUS);
$classificationSet->setDate(new DateTime());
$classificationSet->setSampleSize(1500);
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

echo 'done';

// ------ Utility Functions
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
            $word = str_replace(array(':)', ':-)', ':p', ':(', ':-(',':', ',', ';', '.', "\'", 'RT', '!', "\""), '', $word);
            $word = mb_strtolower($word, 'UTF8');
        }
    }
    $tweet->setText(implode(' ', $words));
}

