<?php
use SE\Tweet\Classifier;
require_once 'bootstrap.php';


// --------------------------- Configure Env

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');

$em = $doctrine->getEntityManager();

$scheme = 'http://';
$url = 'stream.twitter.com/1/statuses/filter.json';

$username = "bwaine";
$password = "hollabackgirl";

// Blueprint function used to emit tweet objects.

$fn = function($data)
        {
            if (strpos($data, 'text'))
            {
                $data = json_decode($data, true);
                $tweet = new SE\Entity\Tweet();
                $tweet->setText($data['text']);
                $tweet->setTweetId($data['id_str']);
                $tweet->setDate(DateTime::createFromFormat('D M d H:i:s O Y', $data['created_at']));
                $tweet->setUser($data['user']['screen_name']);
                $tweet->setLanguage($data['user']['lang']);

                return $tweet;
            }
        };

$factory = new SE\Tweet\Twitterator($username, $password, $url, $scheme);

$factory->setMethod('POST');
$factory->addTrack('birthday');

$streamer = $factory->getStreamIterator($fn); /* @var $set \SE\Entity\ClassificationSet */

// ---------------------- Configure Job

// Obtain the correct classifier for use on this job
$set = $em->find('SE\Entity\ClassificationSet', 1);

$classifier = new Classifier\Bayes($em, $set);

// Define start / stop time for job to run

$now = new DateTime();
$nowStamp = $now->getTimestamp();
$stopInterval = new DateInterval('PT1M');
$now->add($stopInterval);
$stopStamp = $now->getTimestamp();

// ------------------------------------------- // 

// ------------------------------- DO Job ----/// 

$classifications = array();

while(true)
{
    $itr = 0;
    foreach ($streamer as $tweet) /* @var $tweet \SE\Entity\ClassifiedTweet  */
    {
        if ($tweet instanceof \SE\Tweet\Classifier\IClassifiable)
        {
            if ($tweet->getLanguage() != 'en')
            {
                continue;
            }

            $classifications[] = $classifier->classify($tweet);
        }
    
        if(++$itr % 10 == 0)
        {
            if($stopStamp <= time())
            {
                break 2;
            }
        }
    }
}

$positiveTweets = 0;
$negativeTweets = 0;
$unclassified = 0;
$totalTweets = count($classifications);


foreach($classifications as $c => $set)
{

    if($set['p'] === false && $set['n'] === false)
    {
        $unclassified++;
    }
    elseif($set['p'] === false && intOrFloat($set['n']))
    {
        $negativeTweets++;
    }
    elseif($set['n'] === false && intOrFloat($set['p']))
    {
        $positiveTweets++;
    }
    elseif((intOrFloat($set['n']) && intOrFloat($set['p'])) && ($set['p'] > $set['n']))
    {
        $positiveTweets++;
    }
    elseif((intOrFloat($set['n']) && intOrFloat($set['p'])) && ($set['p'] < $set['n']))
    {
        $negativeTweets++;
    }
    else
    {
        // Equal results
        $unclassified++;
    }
}

function intOrFloat($value)
{
    return (is_int($value) || is_float($value) || $value == 1 || $value == 0);
}

var_dump($positiveTweets);
var_dump($negativeTweets);
var_dump($totalTweets);



