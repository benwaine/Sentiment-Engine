<?php
use SE\Tweet\Classifier;

ini_set('html_errors', 0);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');

// Get the latest basic classification result set and use it set up the training
// records for the Bayes classifier.

$em = $doctrine->getEntityManager();/** @var $em Doctrine\ORM\EntityManager **/
$conn = $em->getConnection();
// Get the current classification set
$query = $em->createQuery('SELECT c FROM SE\Entity\ClassificationSet c ORDER BY c.id DESC');
$classificationSet = $query->getResult();
$set = $classificationSet[0];

$wordStorage = new SE\Tweet\Utility\WordStorage($conn);

$count = 1;

// Get all the tweets in a classification set.
foreach ($set->getTweets() as $classification)
{
    echo 'Tweet: ' . $count++ . "  ";
    $tweet  = $classification->getTweet();
    $text   = $tweet->getText();
    $wordAr = explode(' ', $text);

    // Explode the words in a tweet and check for the presence of the word in the set using a DQL query.
    foreach ($wordAr as $word)
    {
        $wordAr = $wordStorage->getWord($set->getId(), $word);
//        var_dump($wordAr);
//        die;
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