<?php
namespace SE\Tweet\Utility;

/**
 * Uses the Twitter search API to create a training sample of Tweets
 * for use in classifier training.
 *
 * @package    Tweet
 * @subpackage Utility
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Sampler
{

    /**
     * Instance of Zend_Http
     *
     * @var Zend_Http_Client
     */
    private $http;
    /**
     * Search Term used to gather sample.
     *
     * @var string
     */
    private $term;
    /**
     * Number of Tweets to gather
     *
     * @var integer
     */
    private $number;

    /**
     * URL of the well know Twitter endpoint.
     *
     * @var sring
     */
    const TWITTER_SEARCH_URL = "http://search.twitter.com/search.json";

    /**
     * The max number of tweets returned in a Twitter search results page.
     *
     * @var integer
     */
    const MAX_PAGE = 100;

    /**
     * Represents a 'happy' / positive sample
     *
     * @var integer
     */
    const HAPPY_SAMPLE = 1;

    /**
     * Represents a 'sad' / negative sample
     *
     * @var integer
     */
    const SAD_SAMPLE = 2;

    /**
     * Initialises an instance of Sampler.
     *
     * @return void
     */
    public function __construct($http, $term, $number)
    {
        $this->http = $http;
        $this->term = $term;

        if($number > 1500)
        {
            throw new \InvalidArgumentException('Sample size is limited to a maximum of 1500 tweets');
        }

        $this->number = $number;
    }

    /**
     * Gathers a sample from the twitter API.
     *
     * @return array
     */
    public function gatherSample()
    {
        $pagesRequired  = $this->pageNumber();

        $positiveTweets = $this->executeSampleGathering(self::HAPPY_SAMPLE, $pagesRequired);
        
        $negativeTweets = $this->executeSampleGathering(self::SAD_SAMPLE, $pagesRequired);

        return array('p' => $positiveTweets, 'n' => $negativeTweets);
    }

    /**
     * Executes a set of HTTP queries to the Twitter API using pagination to obtain
     * the required number of tweets in the sample.
     *
     * @param integer $searchString
     * @param integer $pages
     *
     * @return array
     */
    private function executeSampleGathering($type, $pages)
    {
        $searchString = $this->constructSearchString($type);

        $this->http->setMethod(\Zend_Http_Client::GET);
        $this->http->setHeaders('User-Agent', 'TwitterSentimentEngine/0.5');

        $sample = array();

        for ($x = 1; $x <= $pages; $x++)
        {
            $tweets = $this->sendRequest($searchString, $x);
            $sample = $sample + $tweets;
        }

        return $sample;
    }

    /**
     * Sends the Request to the Twitter API.
     *
     * @return array
     */
    private function sendRequest($searchString, $page)
    {
        $this->http->setUri(\str_replace('<PAGE_REP>', $page, $searchString));
        $response = $this->http->request();
        try
        {
            $tweets = $this->handleResponse($response);

            return $tweets;
        }
        catch (Exception\TooManyApiRequests $e)
        {
            // Back off the number of seconds indicated then resend the request
            sleep($e->getSeconds());

            return $this->sendRequest($searchString, $page);
        }
    }

    /**
     * Handles a response and returns an array of tweet objects.
     *
     * @param \Zend_Http_Response $response Response from the twitter search API.
     *
     * @return array
     */
    private function handleResponse(\Zend_Http_Response $response)
    {
        if ($response->getStatus() != 200)
        {

            switch ($response->getStatus())
            {
                case 420:
                    $retryAr = explode(':', $response->getHeader('Retry-After'));
                    $retry = trim($retryAr[1]);

                    $e = new Exception\TooManyApiRequests('Too many API requests');
                    $e->setSeconds($retry);
                    throw $e;
                    break;
                default:
                    // Fail!
                    $message = $response->getStatus() . " " . $response->getMessage();
                    throw new \RuntimeException('Twitter returned a non 200 Response.' . " $message");
            }
        }
        else
        {
            // Success!
            $responceTweets = \json_decode($response->getBody(), true);
            return $responceTweets['results'];
        }
    }

    /**
     * Constructs the search string
     *
     * @param integer $sampleType Sample type - postive / negative
     *
     * @return string
     */
    private function constructSearchString($sampleType)
    {
        $params = array();

        switch ($sampleType)
        {
            case self::HAPPY_SAMPLE:
                $params['q'] = "q=" . urlencode($this->term) . "+" . urlencode(":)");
                break;
            case self::SAD_SAMPLE:
                $params['q'] = "q=" . urlencode($this->term) . "+" . urlencode(":(");
                break;
            default:
                throw new \InvalidArgumentException('Invalid Sample Type Specified');
        }

        // Results Per Page
        $params['rpp'] = "rpp=" . self::MAX_PAGE;

        // 300 miles radius round conventry
        $params['geocode'] = "geocode=52.405247,-1.508045,300mi";

        //langauge
        $params['lang'] = 'lang=en';

        //Result type - recent rather than popular
        $params['result_type'] = 'result_type=recent';

        // Page - variable
        $params['page'] = "page=<PAGE_REP>";


        $string = self::TWITTER_SEARCH_URL . "?" . implode('&', $params);

        return $string;
    }

    /**
     * Gets the number of pages required to fulfill the requested sample size.
     *
     * @return integer
     */
    private function pageNumber()
    {
        $pages = ceil($this->number / self::MAX_PAGE);

        return $pages;
    }

}

