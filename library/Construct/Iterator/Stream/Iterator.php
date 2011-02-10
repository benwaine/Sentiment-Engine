<?php
namespace Construct\Iterator\Stream;
/**
 * An iterateable stream.
 *
 * @package    Construct
 * @subpackage Iterator
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Iterator implements \Iterator
{
    /**
     * Stream to iterate over.
     *
     * @var resource
     */
    protected $stream;

    /**
     * Initialises an instance of the Iterator class.
     * 
     * @param Resrource $stream An open stream.
     * 
     * @return void 
     */
    public function __construct($stream)
    {
        $this->stream = $stream;
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

