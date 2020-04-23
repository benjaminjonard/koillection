<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Collection;
use App\Model\Search;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    public function findChildrenByAlbumId(string $id) : iterable
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->where('a.parent = :id')
            ->setParameter('id', $id)
            ->leftJoin('a.image', 'i')
            ->addSelect('partial i.{id, path}')
        ;

        return $qb->getQuery()->getResult();
    }
}
