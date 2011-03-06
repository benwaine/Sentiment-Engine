<?php

use SE\Tweet\Classifier;

ini_set('html_errors', 0);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');

// Get the latest basic classification result set and use it set up the training
// records for the Bayes classifier.

$em = $doctrine->getEntityManager();/** @var $em Doctrine\ORM\EntityManager **/
$query = $em->createQuery('SELECT c FROM SE\Entity\ClassificationSet c ORDER BY c.id DESC');

$classificationSet = $query->getResult();

$set = $classificationSet[0];

$em->persist($set);

$count = 1;

// Get all the tweets in a classification set.
foreach ($set->getTweets() as $classification)
{
    echo 'Tweet: ' . $count++ . "  ";

    $tweet = $classification->getTweet();

    $text  = $tweet->getText();

    $wordAr = explode(' ', $text);

    // Explode the words in a tweet and check for the presence of the word in the set using a DQL query.
    foreach ($wordAr as $word)
    {        
        // Query to get a word object from the database.
        $query = $em->createQuery('SELECT w FROM SE\Entity\Word w WHERE w.word = ?1 AND w.classificationSet = ?2');
        $query->setParameter(1, $word);
        $query->setParameter(2, $set->getID());
        $resAr = $query->getResult();

       // \Doctrine\Common\Util\Debug::dump($resAr); //die;
       // echo $resAr[0]->getClassificationSet()->getDate()->format('Y'); die;


        // Fetch single result returns an error if no result is found
        // Check check if exactly one result was returned
        if(count($resAr) != 1)
        {
            $wordOb = null;
        }
        else
        {
            $wordOb = $resAr[0];
        }

        // If no result is found create a new word ob
        if (is_null($wordOb))
        {   
            echo 'N ';
            // No word was found. Create a word object and link it to the current
            // set.
            $wordOb = new SE\Entity\Word();
            $wordOb->setWord($word);
            $wordOb->addAppearence($classification->getClassificationResult());
            $set->addWord($wordOb);
            
            //$em->persist($wordOb); // @bug this line shouldnt be required.

            // Persiting the word seperatly does not
            // re associate the word with the set. Instead add word set to get
            // the back referece from the set method
        }
        else
        {
            echo 'O ';
            // Word is already associated with the current set as it was found
            // via the DQL query.
            $wordOb->addAppearence($classification->getClassificationResult());
        }

        try
        {
            $em->flush(); 
            $em->detach($wordOb); // <-- detaches each word at the end of the for loop.
            //$em->clear(); <-- Clears the set in addition to the words
            //if($count == 10) die;
        }
        catch(Exception $e)
        {
            var_dump($e->getMessage());
            echo $word;
            die;
        }
    
        unset($word);
        unset($wordOb);
    }

    echo "\n";
}