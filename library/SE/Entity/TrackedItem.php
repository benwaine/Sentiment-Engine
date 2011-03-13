<?php
namespace SE\Entity;
/**
 * Descibed events tracked and to be tracked within the system.
 *
 * @package    Tweet
 * @subpackage Tracking
 * @author     Ben Waine
 */
class TrackedItem
{
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
        return $this->tracking;
    }

    /**
     * Sets if the item has been tracked.
     *
     * @param bool $tracking
     *
     * @return void
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
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


}
