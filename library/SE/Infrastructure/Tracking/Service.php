<?php
namespace SE\Infrastructure\Tracking;

use SE\Entity;
use Doctrine\ORM;
use SE\Entity\TrackingItem as TrackingItem;


/**
 * Tracking Service - Manages the tracking of terms
 *
 * @package  Tracking
 * @subpacke Service
 * @author   Ben Waine <ben@ben-waine.co.uk>
 */
class Service
{

    /**
     * Doctrine 2 Entity Manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Initalises the Tracking Service.
     *
     * @param ORM\EntityManager $em
     *
     * @return void
     */
    public function __construct(ORM\EntityManager $em)
    {
        $this->entityManager = $em;
    }

    /**
     * Returns a tracking item by its ID.
     *
     * @param int $id ID of Term
     *
     * @return SE\Entity\TrackingItem
     */
    public function getTrackingItem($id)
    {
        $item = $this->entityManager->find('SE\Entity\TrackingItem', $id);

        return $item;
    }

    /**
     * Gets all Tracked Items.
     *
     * @param int $start  Starting record (pagination)
     * @param int $offset Offset: Amount per page (pagination)
     * @param int $order  Sort Order (One of the defined constant)
     *
     * @return array
     */
    public function getTrackedItems($start, $offset, $order)
    {
        $items = $this->entityManager
                        ->getRepository('SE\Entity\TrackingItem')
                        ->getPagedTrackingItems($start, $offset, $order);

        return $items;
    }

    /**
     * Gets all resources tracked.
     *
     * @return array
     */
    public function getPendingTrackingItems()
    {
        $nontracked = $this->entityManager
                        ->getRepository('SE\Entity\TrackingItem')
                        ->getNonTrackedItems();

        return $nontracked;
    }

    /**
     * Adds a tracking item to the database.
     *
     * @return void
     */
    public function addTrackingItem($values)
    {
        $trackingItem = $this->entityManager->getRepository('SE\Entity\TrackingItem')->findBy(array('term' => $values['tracking_request']));

        if(empty($trackingItem))
        {
            $trackingItem = new Entity\TrackingItem();
            $trackingItem->setRequestDate(new \DateTime());
            $trackingItem->setTerm($values['tracking_request']);
            $trackingItem->setUpdated(new \DateTime());
            $trackingItem->setFullfilmentState(TrackingItem::STATUS_NEW);
            $this->entityManager->persist($trackingItem);
            $this->entityManager->flush();
        }
        else
        {
            throw new Exception('Term Already Tracked');
        }

        return $trackingItem;
    }

    /**
     * Changes the tracking fulfillment status of a tracking item
     *
     * @return void
     */
    public function changeFulfillmentStatus($value, $fulfilled = false)
    {
        if(!isset($value['content']['id']))
        {
            throw new \InvalidArgumentException('No ID in content Array');
        }

        $rep = $this->entityManager->getRepository('SE\Entity\TrackingItem');

        $item = $rep->find($value['content']['id']);

        if(is_null($item))
        {
            throw new Exception('No Item Tracked With This ID');
        }
        else
        {
            if($fulfilled)
            {
                  $state = TrackingItem::STATUS_FINAL;
            }
            else
            {
              $state = ($value['draft']) ? TrackingItem::STATUS_NEW : TrackingItem::STATUS_PROCESSING;
            }
            
            $item->setFullfilmentState($state);
            $item->setUpdated(new \DateTime());

            $this->entityManager->flush();
        }
    }

    /**
     * 
     */
    public function changeSamplingStatus()
    {
        
    }

}

