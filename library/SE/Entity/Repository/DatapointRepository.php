<?php
namespace SE\Entity\Repository;
use SE\Entity\TrackingItem as TrackingItem;
use Doctrine\ORM\EntityRepository as Repository;
/**
 * Contains custom queries to retrieve datapoints
 *
 * @package    Tracking
 * @subpackage Datapoint
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class DatapointRepository extends Repository
{

    public function getPagedDatapoints($termId, $start, $pageSize)
    {
        $queryB = $this->getEntityManager()->createQueryBuilder();

        $queryB->select('dp')
                ->from('SE\Entity\DataPoint', 'dp')
                ->where('dp.term = ?1')
                ->orderBy('dp.dateTime', 'DESC')
                ->setFirstResult($start)
                ->setMaxResults($pageSize);

        $query = $queryB->getQuery();
        $query->setParameter(1, $termId);
       
        $results = $query->getResult();

        return $results;  
    }
}

