<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Wishlist;
use App\Model\Search\Search;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;

class WishlistRepository extends EntityRepository
{
    public function findAll() : array
    {
        return $this
            ->createQueryBuilder('w')
            ->orderBy('w.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

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

    public function findForSearch(Search $search) : array
    {
        $qb = $this
            ->createQueryBuilder('w')
            ->orderBy('w.name', 'ASC')
        ;

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(w.name) LIKE LOWER(:term)')
                ->setParameter('term', '%'.$search->getTerm().'%')
            ;
        }

        if ($search->getCreatedAt() instanceof \DateTime) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('w.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
