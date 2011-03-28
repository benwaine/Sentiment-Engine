<?php
namespace SE\Infrastructure\Tracking;

/**
 * Handles job selection from the jobs que.
 *
 * @package    Tracking
 * @subpackage API
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class APIJobClient
{

    /**
     * XML Feed of the job que.
     *
     * @var string
     */
    private $feed;
    /**
     * The location of the job que feed resource.
     *
     * @var string
     */
    private $feedLocation;
    /**
     * The http object
     * 
     * @var Zend_Http_Client
     */
    private $http;

    /**
     * Initialises an instance of the APIJobClient class
     *
     * @param array            $config Config Array
     * @param Zend_Http_Client $http   HTTP Transport
     *
     * @return void
     */
    public function __construct($config, \Zend_Http_Client $http)
    {
        $this->http = $http;
        $this->feedLocation = $config['location'];
    }

    /**
     * Get a job from the job que.
     *
     * @return array
     */
    public function getJob()
    {
        $this->loadJobFeed();

        $job = $this->selectJob();

        $reserved = $this->reserveJob($job);

        if ($reserved)
        {
            return $job;
        }
        else
        {
            $this->getJob();
        }
    }

    /**
     * Register the job as complete with the fulfillment resource.
     *
     * @return bool
     */
    public function registerCompleteJob($job)
    {
        if (!isset($job['link']['self']))
        {
            throw new \InvalidArgumentException('Invalid Job');
        }

        $this->http->resetParameters(true);
        $this->http->setUri($job['link']['self']);
        $this->http->setMethod(\Zend_Http_Client::DELETE);
        $response = $this->http->request();

        return ($response->getStatus() == 204);
    }

    /**
     * Load the job feed from the remote location
     *
     * @return void
     */
    private function loadJobFeed()
    {
        $this->http->setUri($this->feedLocation);
        $this->http->setMethod(\Zend_Http_Client::GET);
        $response = $this->http->request();

        if ($response->getStatus() == 200)
        {
            $dom = new \DomDocument();
            $dom->preserveWhiteSpace = false;
            $dom->loadXml($response->getRawBody());

            $elements = $dom->getElementsByTagName('entry');

            $mAr = array();

            foreach ($elements as $elm)
            {
                $mAr[] = $this->recurseToArray($elm, array());
            }
            $this->feed = $mAr;
        }
        else
        {
            throw new RuntimeException('Job Que Unreachable');
        }
    }

    /**
     * Select a job from the que.
     *
     * @return array
     */
    private function selectJob()
    {
        if (!isset($this->feed))
        {
            throw new RuntimeException('Feed not initited in APIJobClient');
        }

        while (!is_null($item = array_pop($this->feed)))
        {
            if (!isset($item['app:control']['app:draft']) || $item['app:control']['app:draft'] == 'no')
            {
                continue;
            }
            else
            {
                return $item;
            }
        }
    }

    /**
     * Negotiate a reservation of a job with the fulfillment resource.
     *
     * @param array $job
     *
     * @return bool
     */
    private function reserveJob(array $job)
    {

        if (!isset($job['link']['self']))
            throw new \InvalidArgumentException('Job Is invalid');

        // Get the Job

        $this->http->resetParameters(true);
        $this->http->setUri($job['link']['self']);

        $response = $this->http->request(\Zend_Http_Client::GET);

        if ($response->getStatus() != 200)
            throw new \RuntimeException('Invalid response from webservice');

        $etag = $response->getHeader('Etag');

        $dom = new \DomDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($response->getRawBody());

        $elements = $dom->getElementsByTagName('entry');
        $entry = $this->recurseToArray($elements->item(0), array());

        // Reserve The Job
        $this->http->resetParameters(true);
        $this->http->setUri($job['link']['self']);
        $this->http->setMethod(\Zend_Http_Client::PUT);
        $this->http->setRawData($this->removeAppDraft($dom));
        $this->http->setHeaders('Etag', $etag);
        $response = $this->http->request();

        return ($response->getStatus() == 200);
    }

    private function removeAppDraft(\DomDocument $domDocument)
    {
        $tag = $domDocument->getElementsByTagNameNS('http://www.w3.org/2007/app', 'control');
        
        $tag->item(0)->parentNode->removeChild($tag->item(0));

        return $domDocument->saveXML();
    }

    /**
     * Recurivley parses an xml structure into an array. Does NOT
     * parse attricutes.
     *
     * @param DOMElement $element Element to parse
     * @param array      $array Array to parse into
     *
     * @return array
     */
    private function recurseToArray($element, array $array)
    {
        $name = $element->nodeName;
        $value = $element->nodeValue;
        $type = $element->nodeType;
        $len = $element->childNodes->length;

        if ($element->hasChildNodes())
        {
            foreach ($element->childNodes as $node)
            {
                if ($node->nodeType == XML_TEXT_NODE)
                {
                    return $node->nodeValue;
                }
                else
                {
                    if ($node->nodeName == 'link' && !is_null($node->attributes->getNamedItem('rel')))
                    {
                        $array[$node->nodeName][$node->attributes->getNamedItem('rel')->nodeValue] = $node->attributes->getNamedItem('href')->nodeValue;
                    }
                    else
                    {
                        $array[$node->nodeName] = $this->recurseToArray($node, array());
                    }
                }
            }
        }
        else
        {
            return $element->nodeValue;
        }

        return $array;
    }

}

