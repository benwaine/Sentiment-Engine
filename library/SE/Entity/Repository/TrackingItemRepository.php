<?php
namespace SE\Entity\Repository;
use SE\Entity\TrackingItem as TrackingItem;
use Doctrine\ORM\EntityRepository;

/**
 * Tracking Repository - a place for DQL queries related to tracking.
 *
 * @package    Infrastructure
 * @subpackage Tracking
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class TrackingItemRepository extends EntityRepository
{
    /**
     * Query Return Order - Standard Order: Order inserted into the database.
     *
     * @var int
     */
    const STANDARD_ORDER = 1;

    /**
     * Query Return Order - Date Order: Recently edited returned first.
     *
     * @var int
     */
    const DATE_ORDER = 2;

    /**
     * Gets non tracked Items from the database.
     *
     * @return array
     */
    public function getNonTrackedItems($start = null, $offset = null, $order = null)
    {

        if (isset($order) && ($order != self::STANDARD_ORDER && $order != self::DATE_ORDER))
        {
            throw new \InvalidArgumentException('Invalid Order Specified In Tracking Query');
        }

        $qb = $this->createQueryBuilder('t');

        $qb->add('where', 't.fulfillmentState != :f1');
        $qb->setParameter('f1', TrackingItem::STATUS_FINAL);

        if(isset($order) && $order == self::DATE_ORDER)
        {
            add('orderBy', 't.updated DESC');
        }

        if(is_null($start))
        {
            $qb->setMaxResults($offset);
            $qb->setFirstResult($start);
        }
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Gets a paginated results for tracked items.
     *
     * @param int $start  Starting index
     * @param int $offset Offset (page size)
     * @param int $order  Order Type - Standard (FIFO) or Date (Most Recently edited first)
     *
     * @return void
     */
    public function getPagedTrackingItems($start, $offset, $order = null)
    {
        if ($order != self::STANDARD_ORDER && $order != self::DATE_ORDER)
        {
            throw new \InvalidArgumentException('Invalid Order Specified In Tracking Query');
        }

        $qb = $this->createQueryBuilder('t');
        $qb->andWhere('t.fulfillmentState =' . TrackingItem::STATUS_FINAL);

        if($offset == self::DATE_ORDER)
        {
            $qb->add('orderBy', 't.updated DESC');
        }

        $qb->setMaxResults($offset);
        $qb->setFirstResult($start);

        $query = $qb->getQuery();

        return $query->getResult();
    }

}