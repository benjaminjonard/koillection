<?php

namespace App\Repository;

use App\Entity\Wishlist;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class WishlistRepository
 *
 * @package App\Repository
 */
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
     * @return array
     */
    public function findAllParent() : array
    {
        return $this
            ->createQueryBuilder('w')
            ->leftJoin('w.image', 'w_i')
            ->addSelect('w_i')
            ->andWhere('w.parent IS NULL')
            ->orderBy('w.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $id
     * @return Wishlist|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(string $id) : ?Wishlist
    {
        return $this
            ->createQueryBuilder('w')
            ->leftJoin('w.wishes', 'wi')
            ->leftJoin('w.children', 'ch')
            ->leftJoin('wi.image', 'wi_i')
            ->leftJoin('ch.image', 'ch_i')
            ->addSelect('wi, ch, wi_i, ch_i')
            ->where('w.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
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

        $id = "'" . $id . "'";

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

        $excluded = array_column($this->_em->createNativeQuery($sql, $rsm)->getResult(), "id");

        return $this
            ->createQueryBuilder('w')
            ->orderBy('w.name', 'ASC')
            ->where('w NOT IN  (:excluded)')
            ->setParameter('excluded', $excluded)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return int
     */
    public function countAll() : int
    {
        return $this
            ->createQueryBuilder('w')
            ->select('count(w.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
