<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Model\Search\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class CollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collection::class);
    }

    public function findAll(): array
    {
        return $this
            ->createQueryBuilder('c')
            ->orderBy('c.title', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllExcludingItself(Collection $collection): array
    {
        $id = $collection->getId();
        if (null === $collection->getCreatedAt()) {
            return $this->findAll();
        }

        $id = "'".$id."'";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');

        $table = $this->_em->getClassMetadata(Collection::class)->getTableName();

        $sql = "
            WITH RECURSIVE children AS (
                SELECT c1.id, c1.parent_id
                FROM {$table} c1     
                WHERE c1.id = {$id}
                UNION
                SELECT c2.id, c2.parent_id
                FROM {$table} c2
                INNER JOIN children ch1 ON ch1.id = c2.parent_id
            ) SELECT id FROM children ch2
        ";

        $excluded = array_column($this->_em->createNativeQuery($sql, $rsm)->getResult(), 'id');

        return $this
            ->createQueryBuilder('c')
            ->orderBy('c.title', Criteria::ASC)
            ->where('c NOT IN  (:excluded)')
            ->setParameter('excluded', $excluded)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllWithItems(): array
    {
        return $this
            ->createQueryBuilder('c')
            ->leftJoin('c.items', 'i')
            ->addSelect('i')
            ->leftJoin('c.children', 'ch')
            ->addSelect('ch')
            ->orderBy('c.title', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllWithChildren(): array
    {
        return $this
            ->createQueryBuilder('c')
            ->leftJoin('c.children', 'ch')
            ->addSelect('ch')
            ->orderBy('c.title', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWithItemsAndData(string $id): ?Collection
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('c.items', 'i')
            ->leftJoin('i.data', 'd')
            ->addSelect('i, d')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findForSearch(Search $search): array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->orderBy('c.title', Criteria::ASC)
        ;

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(c.title) LIKE LOWER(:term)')
                ->setParameter('term', '%'.$search->getTerm().'%')
            ;
        }

        if ($search->getCreatedAt() instanceof \DateTimeImmutable) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('c.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function suggestItemsTitles(Collection $collection): array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('c.itemsTitle')
            ->distinct()
            ->where('c.parent = :parent')
            ->andWhere('c.itemsTitle IS NOT NULL')
            ->setParameter('parent', $collection->getParent())
        ;

        if (null !== $collection->getItemsTitle()) {
            $qb
                ->andWhere('c.itemsTitle != :itemsTitle')
                ->setParameter('itemsTitle', $collection->getItemsTitle())
            ;
        }

        return array_column($qb->getQuery()->getArrayResult(), 'itemsTitle');
    }

    public function suggestChildrenLabels(Collection $collection): array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('config.label')
            ->distinct()
            ->leftJoin('c.childrenDisplayConfiguration', 'config')
            ->where('c.parent = :parent')
            ->andWhere('config.label IS NOT NULL')
            ->setParameter('parent', $collection->getParent())
        ;

        if (null !== $collection->getChildrenDisplayConfiguration()->getLabel()) {
            $qb
                ->andWhere('config.label != :label')
                ->setParameter('label', $collection->getChildrenDisplayConfiguration()->getLabel())
            ;
        }

        return array_column($qb->getQuery()->getArrayResult(), 'label');
    }
}
