<?php
namespace SE\Tweet\Utility\Exception;
/**
 * An Exception thrown when an API call has returned a 420 response code
 * indicating the client should wait before retrying.
 *
 * @package    Tweet
 * @subpackage Utility
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class TooManyApiRequests extends \RuntimeException
{
    /**
     * Number of seconds the client is required to back off before retrying.
     *
     * @var integer
     */
    private $seconds;

    /**
     * Set the number of seconds to wait before retrying the request
     *
     * @param integer $seconds Seconds to wait
     *
     * @return void
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;
    }

    /**
     * Get the number of seconds the client should wait before retrying.
     *
     * @return integer
     */
    public function getSeconds()
    {
        return $this->seconds;
    }
}

