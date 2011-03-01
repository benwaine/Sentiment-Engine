<?php

use SE\Tweet\Classifier;

ini_set('html_errors', 0);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');

$em = $doctrine->getEntityManager();/** @var $em Doctrine\ORM\EntityManager * */
$query = $em->createQuery('SELECT c FROM SE\Entity\ClassificationSet c ORDER BY c.id DESC');

$classificationSet = $query->getResult();

$set = $classificationSet[0];

foreach ($set->getTweets() as $classification)
{
    $tweet = $classification->getTweet();
    $text  = $tweet->getText();

    $wordAr = explode(' ', $text);

    foreach ($wordAr as $word)
    {
        $id = md5($set->getId() . $word);
        $wordOb = $em->find('SE\Entity\Word', $id);

        if (is_null($wordOb))
        {
            $wordOb = new SE\Entity\Word();
            $wordOb->setId($id);
            $wordOb->setWord($word);
            $wordOb->addAppearence($classification->getClassificationResult());
            $set->addWord($wordOb);
            $em->persist($wordOb);
        }
        else
        {
            $wordOb->setWord($word);
            $wordOb->addAppearence($classification->getClassificationResult());
        }
    }
    $em->flush();
}