<?php

use SE\Tweet\Classifier;

ini_set('html_errors', 0);
ini_set('display_errors', 1);
require_once 'bootstrap.php';

// --------------------------- Configure Env

$bootstrap = $application->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');

$em = $doctrine->getEntityManager();
$twitterConf = $bootstrap->getOption('twitter');

$opts = $bootstrap->getOptions();

$location = array('location' => $opts['api']['classification']);

$client = new SE\Infrastructure\Tracking\APIJobClient($location, new \Zend_Http_Client());



$scheme = 'http://';
$url = 'stream.twitter.com/1/statuses/filter.json';

$username = $twitterConf['username'];
$password = $twitterConf['password'];

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

while (true)
{
    
    $job = $client->getJob();

    $jobObj = $em->find('SE\Entity\TrackingItem', $job['content']['samplingrequest']['id']);

    $factory = new SE\Tweet\Twitterator($username, $password, $url, $scheme);

    $factory->setMethod('POST');
    $factory->addTrack($jobObj->getTerm());

    $streamer = $factory->getStreamIterator($fn); /* @var $set \SE\Entity\ClassificationSet */

    // ---------------------- Configure Job

    $query = $em->createQuery('SELECT cs FROM SE\Entity\ClassificationSet cs WHERE cs.term = ?1');
    $query->setParameter('1', $job['content']['samplingrequest']['id']);

    $result = $query->getResult();

    if (is_array($result))
    {
        $classificationSet = $result[0];
    }
    else
    {
        throw new Exception('Error in classification set');
    }

    $classifier = new Classifier\Bayes($em, $classificationSet);

    // Define start / stop time for job to run

    $now = new DateTime();
    $nowStamp = $now->getTimestamp();
    $stopInterval = new DateInterval('PT10M');
    $now->add($stopInterval);
    $stopStamp = $now->getTimestamp();

// ------------------------------------------- //
// ------------------------------- DO Job ----///

    $classifications = array();

    

    while (true)
    {
        $itr = 0;
        echo '<table class="results">';
        foreach ($streamer as $tweet) /* @var $tweet \SE\Entity\ClassifiedTweet  */
        {
            if ($tweet instanceof \SE\Tweet\Classifier\IClassifiable)
            {
                if ($tweet->getLanguage() != 'en')
                {
                    continue;
                }
                $res = $classifier->classify($tweet);
                $classifications[] = $res;
                
                echo '<tr><td class="text">' . htmlentities($tweet->getText()) . '</td><td> P:  ' . round($res['p'], 2) . '</td><td>N:   ' . round($res['n'], 2) . "</td><td class='end'>". classify($res) ."</td><td class='end'></td><td class='human'>h: </td></tr>";
            }

            if (++$itr == 100)
            {
                    echo "</table>";
                    $client->registerCompleteJob($job);
                    break 3;
            }
        }
    }
}

function classify($set)
{

        if ($set['p'] === false && $set['n'] === false)
        {
            return 'unclassfied';
        }
        elseif ($set['p'] === false && intOrFloat($set['n']))
        {
            return 'negative';
        }
        elseif ($set['n'] === false && intOrFloat($set['p']))
        {
            return 'positive';
        }
        elseif ((intOrFloat($set['n']) && intOrFloat($set['p'])) && ($set['p'] > $set['n']))
        {
            return 'positive';
        }
        elseif ((intOrFloat($set['n']) && intOrFloat($set['p'])) && ($set['p'] < $set['n']))
        {
            return 'negative';
        }
        else
        {
            // Equal results
            return 'unclassified';
        }
}

function intOrFloat($value)
{
    return (is_int($value) || is_float($value) || $value == 1 || $value == 0);
}

function getTimestamp(DateInterval $interval)
{
    $date = new DateTime();
    $date->add($interval);
    return $date->getTimestamp();
}

