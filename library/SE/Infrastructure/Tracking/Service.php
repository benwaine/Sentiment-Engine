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

    /**
     * Adds a tracking item to the database.
     *
     * @return void
     */
    public function addTrackingItem($values)
    {
       $trackingReq = new Entity\TrackedItem();
       $trackingReq->setRequestDate(new \DateTime());
       $trackingReq->setTerm($values['tracking_request']);
       $trackingReq->setTracking(false);
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
        $query = $this->entityManager->createQuery("SELECT t FROM SE\Entity\TrackedItem t WHERE t.trackingDate IS NULL ");

        $query->execute();

        return $query->getResult();
    }
}

