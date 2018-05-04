<?php

namespace App\Repository;

use App\Entity\Wish;
use Doctrine\ORM\EntityRepository;

/**
 * Class WishRepository
 *
 * @package App\Repository
 */
class WishRepository extends EntityRepository
{
    /**
     * @param string $id
     * @return Wish|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(string $id) : ?Wish
    {
        return $this
            ->createQueryBuilder('wi')
            ->where('wi.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return int
     */
    public function countAll() : int
    {
        return $this
            ->createQueryBuilder('wi')
            ->select('count(wi.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
