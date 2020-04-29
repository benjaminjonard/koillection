<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Album;
use App\Model\Search\Search;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\ResultSetMapping;

class AlbumRepository extends EntityRepository
{
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @param Album $album
     * @return array
     */
    public function findAllExcludingItself(Album $album) : array
    {
        $id = $album->getId();
        if ($album->getCreatedAt() === null) {
            return $this->findAll();
        }

        $id = "'".$id."'";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');

        $table = $this->_em->getClassMetadata(Album::class)->getTableName();

        $sql = "
            WITH RECURSIVE children AS (
                SELECT a1.id, a1.parent_id
                FROM $table a1     
                WHERE a1.id = $id
                UNION
                SELECT a2.id, a2.parent_id
                FROM $table a2
                INNER JOIN children ch1 ON ch1.id = a2.parent_id
            ) SELECT id FROM children ch2
        ";

        $excluded = \array_column($this->_em->createNativeQuery($sql, $rsm)->getResult(), "id");

        return $this
            ->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->where('a NOT IN  (:excluded)')
            ->setParameter('excluded', $excluded)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Search $search
     * @return array
     */
    public function findForSearch(Search $search) : array
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
        ;

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(a.title) LIKE LOWER(:term)')
                ->setParameter('term', '%'.$search->getTerm().'%')
            ;
        }

        if ($search->getCreatedAt() instanceof \DateTime) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('a.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
