<?php

namespace App\Repository;

use App\Entity\Collection;
use App\Model\Search;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class ConnectionRepository
 *
 * @package App\Repository
 */
class ConnectionRepository extends EntityRepository
{
    public function countSince(?\DateTime $since) : int
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('count(c.id)')
        ;

        if ($since instanceof \DateTime) {
            $qb
                ->where('c.date > :since')
                ->setParameter('since', $since->format('Y-m-d'))
            ;
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
