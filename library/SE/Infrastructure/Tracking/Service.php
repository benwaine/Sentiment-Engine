<?php
namespace SE\Infrastructure\Tracking;
use Doctine\ORM;
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
        
    }

    /**
     * Gets all resources tracked.
     *
     * @return array
     */
    public function getTrackedItems()
    {

    }



}

