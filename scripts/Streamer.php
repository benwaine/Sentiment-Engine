<?php
define('APPLICATION_ENV', 'development');
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
        $tweet->setTweet($data['text']);
        $tweet->setTweetId($data['id_str']);
        $tweet->setDate(DateTime::createFromFormat('D M d H:i:s O Y', $data['created_at']));
        $tweet->setUser($data['user']['screen_name']);

        return $tweet;
    }

};


$factory = new SE\Tweet\Twitterator($username, $password, $url, $scheme);

$factory->setMethod('POST');
$factory->addTrack(':)');
$factory->addTrack(':-(');

$streamer = $factory->getStreamIterator($fn);

$count  = 0;

$tweets = array();

foreach($streamer as $tweet)
{
     $count++;
     
     if($tweet instanceof \SE\Entity\Tweet)
     {
         $em->persist($tweet);
     }
     
     if($count % 10 == 0)
     {
         $em->flush();
     }
     
}

