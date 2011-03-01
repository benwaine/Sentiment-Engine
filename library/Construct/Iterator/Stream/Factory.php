<?php

namespace Construct\Iterator\Stream;

/**
 * Factory class used to produce stream iterator objects.
 *
 * @package    Construct
 * @subpackage Iterator
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Factory
{

    /**
     * Username.
     *
     * @var string
     */
    protected $username;
    /**
     * Password.
     *
     * @var string
     */
    protected $password;
    /**
     * URL Scheme.
     *
     * @var string
     */
    protected $scheme;
    /**
     * Url.
     *
     * @var string
     */
    protected $url;
    /**
     * HTTP Method.
     *
     * @var String
     */
    protected $method;
    /**
     * Data used in the request that opens the stream.
     *
     * @var string
     */
    protected $data;

    /**
     * Initialises an instance of Construct.
     *
     * @return void.
     */
    public function __construct($username, $password, $url, $scheme)
    {

        $this->username = $username;
        $this->password = $password;
        $this->scheme = $scheme;
        $this->url = $url;
        $this->method = 'GET';
    }

    /**
     * Get HTTP Method in use.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set HTTP Method.
     *
     * @param string $method HTTP Method.
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Get data used in request.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data used in request.
     *
     * @param string $data Data.
     *
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Creates a stream for use by the iterator.
     *
     * @return resource
     */
    protected function createStream()
    {
        $authURL = $this->scheme . $this->username . ":" . $this->password . "@" . $this->url;

        $opts = array(
            'http' => array(
                'method' => $this->method,
                'header' => 'Content-type: application/x-www-form-urlencoded'
            )
        );

        if(!is_null($this->getData()))
        {
            $opts['http']['content'] = $this->getData();
        }

        $context = \stream_context_create($opts);

        $stream = fopen($authURL, 'r', false, $context);

        if(!$stream)
        {
            throw new \RuntimeException('Unable to open stream');
        }
        
        return $stream;
    }

    /**
     * Creates a Stream using the supplied credentials
     *
     * @return
     */
    public function getStreamIterator()
    {
        $iterator = new Iterator($stream);

        return $iterator;
    }

}

