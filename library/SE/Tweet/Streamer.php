<?php
namespace SE\Tweet;
/**
 * Description of Streamer
 *
 * @package    Tweet
 * @subpackage Streamer
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Streamer implements \Iterator
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
     * Tweet Line number.
     *
     * @var integer
     */
    protected $line;

    /**
     * Stream.
     *
     * @var Resource
     */
    protected $stream;

    /**
     * Initialises an instance of Construct.
     *
     * @return void.
     */
    public function __construct($username, $password, $url, $scheme)
    {

        $this->username = $username;
        $this->password = $password;
        $this->scheme   = $scheme;
        $this->url      = $url;

    }

    /**
     * Creates a Stream using the supplied credentials
     *
     * @return
     */
    public function createStream()
    {
        $authURL = $scheme.$username.":".$password."@".$url;

        $opts = array(
            'http' => array(
                'method'  => 'POST',
                'content' => $data,
                'header'  => 'Content-type: application/x-www-form-urlencoded'
            )
        );

        $context = \stream_context_create($opts);

        $stream = fopen($authURL, 'r', false, $context);

        if($stream)
        {
            $this->stream = $stream;
        }
    }

    /**
     * Returns the current index.
     *
     * @return string
     */
    public function current()
    {
        return fgets($this->stream);
    }

    /**
     * Returns the line count.
     *
     * @return integer
     */
    public function key()
    {
        return $this->line;
    }

    /**
     * Increment the line count.
     *
     * @return void
     */
    public function next()
    {
        ++$this->line;
    }

    /**
     * Throws the an exception. This stream is not rewindable.
     *
     * @throws RuntimeException
     *
     * @return void
     */
    public function rewind()
    {
       // throw new \RuntimeException('Not possible to rewind a TweetStreamer');
    }

    /**
     * Checks to see if the stream is valid.
     *
     * @return bool.
     */
    public function valid()
    {
       
        if(!feof($this->stream))
        {
            // Pointer is still valid
            return true;
        }

        // Pointer is not valid
        return false;
    }

    /**
     * If a stream is active it is closed.
     *
     * @return void
     */
    public function __destruct()
    {
        if(isset($this->stream))
        {
            fclose($this->stream);
        }
    }

}

