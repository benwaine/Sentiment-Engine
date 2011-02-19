<?php
use SE\Tweet\Classifier;

ini_set('html_errors', 0);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();
$doctrine  = $bootstrap->getResource('doctrine');

$em = $doctrine->getEntityManager();

$scheme   = 'http://';
//$url      = 'stream.twitter.com/1/statuses/sample.json';
$url      = 'stream.twitter.com/1/statuses/filter.json';

$username = "bwaine";
$password = "hollabackgirl";

// Blueprint function used to emit tweet objects.

$fn = function($data)
{
    if(strpos($data, 'text'))
    {
        $data = json_decode($data, true);
        $tweet = new SE\Entity\Tweet();
        $tweet->setText($data['text']);
        $tweet->setTweetId($data['id_str']);
        $tweet->setDate(DateTime::createFromFormat('D M d H:i:s O Y', $data['created_at']));
        $tweet->setUser($data['user']['screen_name']);

        return $tweet;
    }

};

$factory = new SE\Tweet\Twitterator($username, $password, $url, $scheme);

$factory->setMethod('POST');
$factory->addTrack(':)');
$factory->addTrack(':(');

$streamer = $factory->getStreamIterator($fn);

$classificationSet =  new SE\Entity\ClassificationSet();
$classificationSet->setType(\SE\Entity\ClassificationSet::TYPE_CORPUS);
$classificationSet->setDate(new DateTime());


$manager = new SE\Tweet\Classifier\SampleManager(new SE\Tweet\Classifier\Smiley(), $classificationSet, 5, 5);

$em->persist($classificationSet);

foreach($streamer as $tweet) /* @var $tweet SE\Entity\ClassifiedTweet  */
{
     if($tweet instanceof \SE\Tweet\Classifier\IClassifiable)
     {
         $em->persist($tweet);
         $manager->addToSample($tweet);
         if($manager->isComplete())
         {
             $em->flush();
             break;
         }   
     }
}
