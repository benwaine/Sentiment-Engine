<?php
namespace SE\Entity;
/**
 * A Datapoint represents a point in time were a tracked term was run through
 * the bayesian filter created during the sampling stage.
 *
 * @package    Tracking
 * @subpackage Entity
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class DataPoint
{
    /**
     * DataPoint ID
     *
     * @var int
     */
    private $id;

    /**
     * DateTime of sample
     *
     * @var DateTime
     */
    private $dateTime;

    /**
     * Number of negative tweets recorded during sampling period.
     *
     * @var int
     */
    private $negative;

    /**
     * Number of positive tweets recoirded during the sampling period.
     *
     * @var int
     */
    private $positive;

    /**
     * Number of unclassifed tweets recorded during the sampling peroid
     *
     * @var int
     */
    private $unclassified;

    /**
     * Total number of tweets sampled during the sampling period.
     *
     * @var int
     */
    private $sample;

    /**
     * The term the data point relates to.
     *
     * @var SE\Entity\TrackingItem
     */
    private $term;

    /**
     * Initialises an instance of the Datapoint class.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Get the ID of the Datapoint
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id of the datapoint.
     *
     * @param int $id Data Point ID
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the DateTime of the Datapoint
     *
     * @return DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set the datapoint DateTime
     *
     * @param DateTime $datetime DateTime of sample.
     *
     * @return void
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    

    /**
     * Get the number of negative tweets sampled.
     *
     * @return int
     */
    public function getNegative()
    {
        return $this->negative;
    }

    /**
     * Set the number of Negative tweets sampled.
     *
     * @param int $negative Negative Tweets
     *
     * @return void
     */
    public function setNegative($negative)
    {
        $this->negative = $negative;
    }

    /**
     * Get the number of positive twet sampled.
     *
     * @return int
     */
    public function getPositive()
    {
        return $this->positive;
    }

    /**
     * Set the number of positve tweets sampled.
     *
     * @param int $positive Positive tweets
     *
     * @return void
     */
    public function setPositive($positive)
    {
        $this->positive = $positive;
    }

    /**
     * Get the number of unclassied tweets.
     *
     * @return int
     */
    public function getUnclassifed()
    {
        return $this->unclassifed;
    }

    /**
     * Set the unclassified tweets
     * 
     * @param int $unclassifed Unclassified Tweets
     *
     * @return void
     */
    public function setUnclassifed($unclassifed)
    {
        $this->unclassifed = $unclassifed;
    }

    /**
     * Get the number of sampled tweets
     *
     * @return int
     */
    public function getSampled()
    {
        return $this->sampled;
    }

    /**
     * Set the sampled tweets
     *
     * @param int $sampled Sampled Tweets
     *
     * @return void
     */
    public function setSampled($sampled)
    {
        $this->sampled = $sampled;
    }

    /**
     * Get the term associated with the datapoint.
     *
     * @return SE\Entity\TrackingItem
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set the term associated with the data point.
     *
     * @param SE\Entity\TrackingTerm $term
     *
     * @return void
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }
}
