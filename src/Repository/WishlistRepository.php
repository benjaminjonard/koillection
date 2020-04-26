<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Wishlist;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;

class WishlistRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findAll() : array
    {
        return $this
            ->createQueryBuilder('w')
            ->orderBy('w.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Wishlist $wishlist
     * @return array
     */
    public function findAllExcludingItself(Wishlist $wishlist) : array
    {
        $id = $wishlist->getId();
        if ($wishlist->getCreatedAt() === null) {
            return $this->findAll();
        }

        $id = "'".$id."'";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');

        $table = $this->_em->getClassMetadata(Wishlist::class)->getTableName();

        $sql = "
            WITH RECURSIVE children AS (
                SELECT w1.id, w1.parent_id
                FROM $table w1     
                WHERE w1.id = $id
                UNION
                SELECT w2.id, w2.parent_id
                FROM $table w2
                INNER JOIN children ch1 ON ch1.id = w2.parent_id
            ) SELECT id FROM children ch2
        ";

        $excluded = \array_column($this->_em->createNativeQuery($sql, $rsm)->getResult(), "id");

        return $this
            ->createQueryBuilder('w')
            ->orderBy('w.name', 'ASC')
            ->where('w NOT IN  (:excluded)')
            ->setParameter('excluded', $excluded)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findChildrenByWishlistId(string $id) : iterable
    {
        $qb = $this
            ->createQueryBuilder('w')
            ->where('w.parent = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getResult();
    }
}
