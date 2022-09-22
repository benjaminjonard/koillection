<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
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

    public function suggestItemsLabels(Collection $collection): array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('config.label')
            ->distinct()
            ->leftJoin('c.itemsDisplayConfiguration', 'config')
            ->where('c.parent = :parent')
            ->andWhere('config.label IS NOT NULL')
            ->setParameter('parent', $collection->getParent())
        ;

        if (null !== $collection->getItemsDisplayConfiguration()->getLabel()) {
            $qb
                ->andWhere('config.label != :label')
                ->setParameter('label', $collection->getItemsDisplayConfiguration()->getLabel())
            ;
        }

        return array_column($qb->getQuery()->getArrayResult(), 'label');
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

    public function findForOrdering(Collection $collection, bool $asArray = false): array
    {
        if ($collection->getItemsDisplayConfiguration()->getSortingProperty()) {
            // Get ordering value
            $subQuery = $this->_em
                ->createQueryBuilder()
                ->select('datum.value')
                ->from(Datum::class, 'datum')
                ->where('datum.collection = child')
                ->andWhere('datum.label = :label')
                ->andWhere('datum.type IN (:types)')
                ->setMaxResults(1)
            ;

            $qb = $this
                ->createQueryBuilder('child')
                ->addSelect("({$subQuery}) AS orderingValue, data")
                ->where('child.parent = :parent')
                ->setParameter('parent', $collection->getId())
                ->setParameter('label', $collection->getItemsDisplayConfiguration()->getSortingProperty())
                ->setParameter('types', DatumTypeEnum::AVAILABLE_FOR_ORDERING)
            ;

            // If list, preload datum used for ordering and in columns
            if (DisplayModeEnum::DISPLAY_MODE_LIST === $collection->getChildrenDisplayConfiguration()->getDisplayMode()) {
                $qb
                    ->leftJoin('child.data', 'data', 'WITH', 'data.label = :label OR data.label IN (:labels) OR data IS NULL')
                    ->setParameter('labels', $collection->getItemsDisplayConfiguration()->getColumns())
                ;
            } else {
                // Else only preload datum used for ordering
                $qb
                    ->leftJoin('child.data', 'data', 'WITH', 'data.label = :label OR data IS NULL')
                ;
            }

            $results = $asArray ? $qb->getQuery()->getArrayResult() : $qb->getQuery()->getResult();

            return array_map(static function ($result) use ($asArray) {
                $child = $result[0];
                if ($asArray) {
                    $child['orderingValue'] = $result['orderingValue'];
                } else {
                    $child->setOrderingValue($result['orderingValue']);
                }

                return $child;
            }, $results);
        }

        $qb = $this
            ->createQueryBuilder('child')
            ->where('child.parent = :parent')
            ->setParameter('parent', $collection->getId())
        ;

        if (DisplayModeEnum::DISPLAY_MODE_LIST === $collection->getChildrenDisplayConfiguration()->getDisplayMode()) {
            $qb
                ->addSelect('data')
                ->leftJoin('child.data', 'data', 'WITH', 'data.label IN (:labels) OR data IS NULL')
                ->setParameter('labels', $collection->getChildrenDisplayConfiguration()->getColumns())
            ;
        }

        return $asArray ? $qb->getQuery()->getArrayResult() : $qb->getQuery()->getResult();
    }

    public function computePrices(Collection $collection): array
    {
        $cast = match ($this->_em->getConnection()->getDatabasePlatform()->getName()) {
            'postgresql' => 'DOUBLE PRECISION',
            'mysql' => 'DECIMAL(12, 2)',
        };

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('label', 'label');
        $rsm->addScalarResult('price', 'price');

        $id = $collection->getId();

        $sql = "
            WITH RECURSIVE prices AS (
                SELECT c1.id, c1.parent_id, c1.final_visibility, d1.label AS dLabel, d1.value AS dValue
                FROM koi_collection c1
                JOIN koi_item i1 ON i1.collection_id = c1.id               
                JOIN koi_datum d1 ON d1.item_id = i1.id AND d1.type = 'price'
                WHERE c1.id = '$id'
                
                UNION
                
                SELECT c2.id, c2.parent_id, c2.final_visibility, d2.label AS dLabel, d2.value AS dValue
                FROM koi_collection c2
                JOIN koi_item i2 ON i2.collection_id = c2.id
                JOIN koi_datum d2 ON d2.item_id = i2.id AND d2.type = 'price'
                INNER JOIN prices p1 ON p1.id = c2.parent_id
                
            ) SELECT dLabel AS \"label\", SUM(CAST(dValue AS $cast)) AS price FROM prices p2
            GROUP BY dLabel
        ";

        return $this->_em->createNativeQuery($sql, $rsm)->getArrayResult();
    }
}
