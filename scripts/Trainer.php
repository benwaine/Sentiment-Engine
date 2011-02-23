<?php
use SE\Tweet\Classifier;

ini_set('html_errors', 0);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');

$em = $doctrine->getEntityManager(); /** @var  **/

$query = $em->createQuery('SELECT c FROM SE\Entity\ClassificationSet c ORDER BY c.id DESC');
$classificationSet = $query->getResult();

$set = $classificationSet[0];

foreach($set->getTweets() as $tweet)
{
    echo $tweet->getText();
}


?>
