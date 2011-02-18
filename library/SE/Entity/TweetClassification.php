<?php
namespace SE\Entity;
/**
 * Contains the result of a tweet classification.
 *
 * @package    Tweet
 * @subpackage Classification
 * @author     Ben Waine <ben@ben-wane.co.uk>
 */
class TweetClassification
{
    
    /**
     * Classified Tweet Id.
     *
     * @var integer
     */
    protected $classifiedTweetId;
    
    /**
     * Tweet the classification pertains to.
     *
     * @var Tweet
     */
    protected $tweet;

    /**
     * Classification Type. Should be one of the above constants.
     *
     * @var integer
     */
    protected $classificationType;

    /**
     * Result of classification.
     *
     * @var integer
     */
    protected $classificationResult;

    /**
     * Classification DateTime
     *
     * @var DateTime
     */
    protected $classificationTime;

    /**
     * Sets a reference to the classified tweet.
     *
     * @param Tweet $tweet Tweet to be classifed
     *
     * return void
     */
    public function __construct($tweet)
    {
        $this->setTweet($tweet);
    }

    /**
     * Get the id.
     *
     * @return integer
     */
    public function getClassifiedTweetId()
    {
        return $this->classifiedTweetId;
    }

    /**
     * Set the id.
     *
     * @param integer $classifiedTweetId ID
     *
     * @return void
     */
    public function setClassifiedTweetId($classifiedTweetId)
    {
        $this->classifiedTweetId = $classifiedTweetId;
    }

    /**
     * Get the classified tweet
     *
     * @return Tweet
     */
    public function getTweet()
    {
        return $this->tweet;
    }

    /**
     * Set the classified Tweet
     *
     * @param Tweet $tweet Classified Tweet
     *
     * @return void
     */
    private function setTweet($tweet)
    {
        $this->tweet = $tweet;
    }


    /**
     * Gets the classification Type
     *
     * @return integer
     */
    public function getClassificationType()
    {
        return $this->classificationType;
    }

    /**
     * Sets the classification Type.
     *
     * @param integer $classificationType Type
     *
     * @return void
     */
    public function setClassificationType($classificationType)
    {
        $this->classificationType = $classificationType;
    }

    /**
     * Get the classification resul.
     *
     * @return integer
     */
    public function getClassificationResult()
    {
        return $this->classificationResult;
    }

    /**
     * Sets the classification Result
     *
     * @param integer $classificationResult
     *
     * @return void
     */
    public function setClassificationResult($classificationResult)
    {
        $this->classificationResult = $classificationResult;
    }

    /**
     * Gte classification Time.
     *
     * @return DateTime
     */
    public function getClassificationTime()
    {
        return $this->classificationTime;
    }

    /**
     * Set the classification Time
     *
     * @param DateTime $classificationTime
     */
    public function setClassificationTime($classificationTime)
    {
        $this->classificationTime = $classificationTime;
    }

}

