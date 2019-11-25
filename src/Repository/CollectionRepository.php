<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Model\Search;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class CollectionRepository
 *
 * @package App\Repository
 */
class CollectionRepository extends EntityRepository
{
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    /**
     * @return array
     */
    public function findAll() : array
    {
        return $this
            ->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Collection $collection
     * @return array
     */
    public function findAllExcludingItself(Collection $collection) : array
    {
        $id = $collection->getId();
        if ($collection->getCreatedAt() === null) {
            return $this->findAll();
        }

        $id = "'".$id."'";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');

        $table = $this->_em->getClassMetadata(Collection::class)->getTableName();

        $sql = "
            WITH RECURSIVE children AS (
                SELECT c1.id, c1.parent_id
                FROM $table c1     
                WHERE c1.id = $id
                UNION
                SELECT c2.id, c2.parent_id
                FROM $table c2
                INNER JOIN children ch1 ON ch1.id = c2.parent_id
            ) SELECT id FROM children ch2
        ";

        $excluded = \array_column($this->_em->createNativeQuery($sql, $rsm)->getResult(), "id");

        return $this
            ->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->where('c NOT IN  (:excluded)')
            ->setParameter('excluded', $excluded)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $id
     * @return Collection|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findWithItems($id) : ?Collection
    {
        return $this
            ->createQueryBuilder('c')
            ->leftJoin('c.items', 'i')
            ->addSelect('i')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /**
     * @return array
     */
    public function findAllWithItems() : array
    {
        return $this
            ->createQueryBuilder('c')
            ->leftJoin('c.items', 'i')
            ->addSelect('i')
            ->leftJoin('c.children', 'ch')
            ->addSelect('ch')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array
     */
    public function findAllWithChildren() : array
    {
        return $this
            ->createQueryBuilder('c')
            ->leftJoin('c.children', 'ch')
            ->addSelect('ch')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find a collection, with children if specified.
     *
     * @param string $id
     * @param bool $withData
     * @return Collection|null
     */
    public function findById(string $id, bool $withData = false) : ?Collection
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
        ;

        if (true === $withData) {
            $qb
                ->leftJoin('c.items', 'i')
                ->leftJoin('i.data', 'd')
                ->leftJoin('d.image', 'd_i')
                ->addSelect('i, d, d_i')
            ;
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findChildrenByCollectionId(string $id) : iterable
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->where('c.parent = :id')
            ->setParameter('id', $id)
            ->leftJoin('c.image', 'i')
            ->addSelect('partial i.{id, path}')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findAllParent() : array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.image', 'c_i')
            ->addSelect('c_i')
            ->andWhere('c.parent IS NULL')
            ->orderBy('c.title', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Search $search
     * @return array
     */
    public function findForSearch(Search $search) : array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.image', 'c_i')
            ->addSelect('c_i')
            ->orderBy('c.title', 'ASC')
        ;

        if (\is_string($search->getSearch()) && !empty($search->getSearch())) {
            $qb
                ->andWhere('LOWER(c.title) LIKE LOWER(:search)')
                ->setParameter('search', '%'.$search->getSearch().'%')
            ;
        }

        if ($search->getCreatedAt() instanceof \DateTime) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('c.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int
     */
    public function countAll() : int
    {
        return $this
            ->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function suggestItemsTitles(Collection $collection) : array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('c.itemsTitle')
            ->distinct()
            ->where('c.parent = :parent')
            ->andWhere('c.itemsTitle IS NOT NULL')
            ->setParameter('parent', $collection->getParent())
        ;

        if ($collection->getItemsTitle() !== null) {
            $qb
                ->andWhere('c.itemsTitle != :itemsTitle')
                ->setParameter('itemsTitle', $collection->getItemsTitle())
            ;
        }

        return \array_column($qb->getQuery()->getArrayResult(), "itemsTitle");
    }

    public function suggestChildrenTitles(Collection $collection) : array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('c.childrenTitle')
            ->distinct()
            ->where('c.parent = :parent')
            ->andWhere('c.childrenTitle IS NOT NULL')
            ->setParameter('parent', $collection->getParent())
        ;

        if ($collection->getChildrenTitle() !== null) {
            $qb
                ->andWhere('c.childrenTitle != :childrenTitle')
                ->setParameter('childrenTitle', $collection->getChildrenTitle())
            ;
        }

        return \array_column($qb->getQuery()->getArrayResult(), "childrenTitle");
    }
}
