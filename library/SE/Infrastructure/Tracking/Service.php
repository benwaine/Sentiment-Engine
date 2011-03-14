<?php
namespace SE\Infrastructure\Tracking;

use SE\Entity;
use Doctrine\ORM;

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

    public function getTrackingItems($start, $offset, $order)
    {
        $items = $this->entityManager
                        ->getRepository('SE\Entity\TrackingItem')
                        ->getPagedTracingItems($start, $offset, $order);

        return $items;
    }

    /**
     * Adds a tracking item to the database.
     *
     * @return void
     */
    public function addTrackingItem($values)
    {
        $trackingReq = new Entity\TrackingItem();
        $trackingReq->setRequestDate(new \DateTime());
        $trackingReq->setTerm($values['tracking_request']);
        $trackingReq->setUpdated(new \DateTime());

        $this->entityManager->persist($trackingReq);
        $this->entityManager->flush();
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

}

