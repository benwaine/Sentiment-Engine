<?php
namespace SE\Entity;
use Construct\HashState as HashState;
/**
 * Descibed events tracked and to be tracked within the system.
 *
 * @package    Tweet
 * @subpackage Tracking
 * @author     Ben Waine
 */
class TrackingItem implements HashState
{
    const STATUS_NEW = 1;

    const STATUS_PROCESSING = 2;

    const STATUS_FINAL = 3;

    const SAMP_IN_PROG = 1;

    const SAMP_NOT_IN_PROG = 2;

    const SAMP_NOT_SAMPLING = 3;

    /**
     * Item ID
     *
     * @var integer
     */
    private $id;

    /**
     * DateTime item tracking request recived.
     *
     * @var DateTime
     */
    private $requestDate;

    /**
     * DateTime Tracking actually began.
     *
     * @var DateTime
     */
    private $trackingDate;

    /**
     * DateTime entry updated.
     *
     * @var DateTime
     */
    private $updated;

    /**
     * The term being tracked.
     *
     * @var string
     */
    private $term;

    /**
     * The State the term is in.
     * Defined by constants above. (New, Processing, Fulfilled)
     *
     * @var int
     */
    private $fulfillmentState;

    /**
     * The Sampling State
     * Defined by constants above (in progress / not in progress)
     *
     * @var int
     */
    private $samplingState;

    /**
     * Gets the ID of the tracked item.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id of the tracked item.
     *
     * @param integer $id ID
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the date the tracking request was made.
     *
     * @return DateTime
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Sets the DateTime tracking request was made.
     *
     * @param DateTime $resquestDate
     *
     * @return void
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;
    }

    /**
     * Gets the DateTime the tracking request as actioned.
     *
     * @return DateTime
     */
    public function getTrackingDate()
    {
        return $this->trackingDate;
    }

    /**
     * Sets the DateTime the tracking request was fullfilled.
     *
     * @param DateTime $trackingDate Date request actioned.
     *
     * @return void
     */
    public function setTrackingDate($trackingDate)
    {
        $this->trackingDate = $trackingDate;
    }

    /**
     * Returns true if tracking has begun on the item.
     *
     * @return bool
     */
    public function isTracked()
    {
        if($this->fulfillmentState == self::STATUS_FINAL)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function isBeingFulFilled()
    {
        if($this->fulfillmentState == self::STATUS_PROCESSING)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Gets the DateTime of the last Update to the Tracked Item.
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Sets the Updated DateTime of the item.
     *
     * @param DateTime $updated DateTime of update.
     *
     * @return void
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get the term tracked.
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Sets the tracking term.
     *
     * @param string $term Term to be tracked.
     *
     * @return void
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }

    /**
     * Gets the fulfillment state of the term.
     *
     * @return int
     */
    public function getFullfilmentState()
    {
        return $this->fulfillmentState;
    }

    /**
     * Sets the Fulfillment state.
     *
     * @param int $fullfilmentState One of the defined fulfillment constants.
     *
     * @return void
     */
    public function setFullfilmentState($fullfilmentState)
    {
        $this->fulfillmentState = $fullfilmentState;
    }

    /**
     * Returns a string used to produce hashes.
     *
     * @return string
     */
    public function getHashString()
    {
        return $this->updated->format('Y-m-d H:i:s') . $this->fulfillmentState;
    }

    /**
     * Sets the sampling state of the tracked term.
     * Defined using the constants above.
     *
     * @param int $state The resource state
     *
     * @return void
     */
    public function setSamplingState($state)
    {
        $this->samplingState = $state;
    }

    /**
     * Get the sampling state of the resource.
     *
     * @return bool
     */
    public function isSamplingInProgress()
    {
        return ($this->samplingState == self::SAMP_IN_PROG);
    }



}
