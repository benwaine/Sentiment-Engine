<?php

use SE\Tweet\Classifier;

ini_set('html_errors', 0);
require_once 'bootstrap.php';

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

$set = $em->find('SE\Entity\ClassificationSet', 1);

$classifier = new Classifier\Bayes($em, $set);

foreach ($streamer as $tweet) /* @var $tweet \SE\Entity\ClassifiedTweet  */
{
    if ($tweet instanceof \SE\Tweet\Classifier\IClassifiable)
    {
        if ($tweet->getLanguage() != 'en')
        {
            continue;
        }

        $classifier->classify($tweet);
    }
}
