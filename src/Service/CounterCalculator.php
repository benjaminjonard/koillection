<?php

declare(strict_types=1);

namespace App\Service;

use App\Doctrine\QueryNameGenerator;
use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class CounterCalculator
 *
 * @package App\Service
 */
class CounterCalculator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var QueryNameGenerator
     */
    private $qng;

    /**
     * CounterCalculator constructor.
     * @param EntityManagerInterface $em
     * @param QueryNameGenerator $qng
     */
    public function __construct(EntityManagerInterface $em, QueryNameGenerator $qng)
    {
        $this->em = $em;
        $this->qng = $qng;
    }

    /**
     * @param array $wishlists
     * @return array
     */
    public function wishlistsCounters(array $wishlists) : array
    {
        return $this->executeQuery($wishlists, ...$this->getWishlistParams());
    }

    /**
     * @param $wislist
     * @return array
     */
    public function wishlistCounters($wislist) : array
    {
        return $this->executeItemQuery($wislist, ...$this->getWishlistParams());
    }

    /**
     * @param array $collections
     * @return array
     */
    public function collectionsCounters(array $collections) : array
    {
        return $this->executeQuery($collections, ...$this->getCollectionParams());
    }

    /**
     * @param $collection
     * @return array
     */
    public function collectionCounters($collection) : array
    {
        return $this->executeItemQuery($collection, ...$this->getCollectionParams());
    }

    /**
     * @return array
     */
    private function getCollectionParams()
    {
        return [
            'children',
            'items',
            'totalChildrenCount',
            'totalItemsCount',
            $this->em->getClassMetadata(Collection::class)->getTableName(),
            $this->em->getClassMetadata(Item::class)->getTableName(),
            'collection_id'
        ];
    }

    /**
     * @return array
     */
    private function getWishlistParams()
    {
        return [
            'children',
            'wishes',
            'totalChildrenCount',
            'totalWishesCount',
            $this->em->getClassMetadata(Wishlist::class)->getTableName(),
            $this->em->getClassMetadata(Wish::class)->getTableName(),
            'wishlist_id'
        ];
    }

    /**
     * @param $entities
     * @param $childrenIndex
     * @param $itemsIndex
     * @param $totalChildrenIndex
     * @param $totalItemsIndex
     * @param $table
     * @param $itemTable
     * @param $parentProperty
     * @return array
     */
    private function executeQuery($entities, $childrenIndex, $itemsIndex, $totalChildrenIndex, $totalItemsIndex, $table, $itemTable, $parentProperty)
    {
        $rsm = new ResultSetMapping();
        $rsm->addIndexByScalar('id');
        $rsm->addScalarResult($childrenIndex, $childrenIndex);
        $rsm->addScalarResult($itemsIndex, $itemsIndex);

        $alias = $this->qng->generateJoinAlias('c');
        $ids = [];
        foreach ($entities as $entitiy) {
            $ids[] = "'".$entitiy->getId()."'";
        }
        $ids = implode(',', $ids);

        $counters = [];
        if (!empty($ids)) {
            $sqlChildrenCounter = $this->getSQLForChildrenCounter($alias, $table);
            $sqlItemsCounter = $this->getSQLForItemsCounter($alias, $table, $itemTable, $parentProperty);

            $sql = "
                SELECT $alias.id as id, ($sqlChildrenCounter) as $childrenIndex, ($sqlItemsCounter) as $itemsIndex 
                FROM $table $alias
                WHERE $alias.id IN ($ids)
            ";

            if ($this->em->getFilters()->isEnabled('visibility')) {
                $sql .= sprintf("AND %s.visibility = '%s'", $alias, VisibilityEnum::VISIBILITY_PUBLIC);
            };

            $counters = $this->em->createNativeQuery($sql, $rsm)->getResult();
        }

        $counters[$totalChildrenIndex] = 0;
        $counters[$totalItemsIndex] = 0;
        foreach ($counters as $key => $counter) {
            if (!\in_array($key, [$totalChildrenIndex, $totalItemsIndex], false)) {
                $counters[$totalChildrenIndex] += $counter[$childrenIndex] + 1;
                $counters[$totalItemsIndex] += $counter[$itemsIndex];
            }
        }

        return $counters;
    }

    /**
     * @param $entity
     * @param $childrenIndex
     * @param $itemsIndex
     * @param $totalChildrenIndex
     * @param $totalItemsIndex
     * @param $table
     * @param $itemTable
     * @param $parentProperty
     * @return array
     */
    public function executeItemQuery($entity, $childrenIndex, $itemsIndex, $totalChildrenIndex, $totalItemsIndex, $table, $itemTable, $parentProperty) : array
    {
        $rsm = new ResultSetMapping();
        $rsm->addIndexByScalar('id');
        $rsm->addScalarResult($childrenIndex, $childrenIndex);
        $rsm->addScalarResult($itemsIndex, $itemsIndex);

        $alias = $this->qng->generateJoinAlias('c');
        $id = "'".$entity->getId()."'";

        $sqlChildrenCounter = $this->getSQLForChildrenCounter($alias, $table);
        $sqlItemsCounter = $this->getSQLForItemsCounter($alias, $table, $itemTable, $parentProperty);

        $sql = "
            SELECT $alias.id as id, ($sqlChildrenCounter) as $childrenIndex, ($sqlItemsCounter) as $itemsIndex 
            FROM $table $alias
            WHERE $alias.id = $id 
            OR $alias.parent_id = $id
        ";

        if ($this->em->getFilters()->isEnabled('visibility')) {
            $sql .= sprintf("AND %s.visibility = '%s'", $alias, VisibilityEnum::VISIBILITY_PUBLIC);
        };

        $counters = $this->em->createNativeQuery($sql, $rsm)->getResult();

        return $counters;
    }

    /**
     * @param string $alias
     * @param $table
     * @return string
     */
    private function getSQLForChildrenCounter(string $alias, $table) : string
    {
        $c1 = $this->qng->generateJoinAlias('c');
        $c2 = $this->qng->generateJoinAlias('c');
        $ch1 = $this->qng->generateJoinAlias('ch');
        $ch2 = $this->qng->generateJoinAlias('ch');

        $sql = "
            WITH RECURSIVE children AS (
                SELECT $c1.id, $c1.parent_id, $c1.visibility
                FROM $table $c1
                WHERE $c1.id = $alias.id
                UNION
                SELECT $c2.id, $c2.parent_id, $c2.visibility   
                FROM $table $c2
                INNER JOIN children $ch1 ON $ch1.id = $c2.parent_id
            ) SELECT COUNT(*) FROM children $ch2 WHERE $alias.id != $ch2.id
        ";

        if ($this->em->getFilters()->isEnabled('visibility')) {
            $sql .= sprintf("AND %s.visibility = '%s'", $ch2, VisibilityEnum::VISIBILITY_PUBLIC);
        };

        return $sql;
    }

    /**
     * @param string $alias
     * @param $table
     * @param $itemTable
     * @param $parentProperty
     * @return string
     */
    private function getSQLForItemsCounter(string $alias, $table, $itemTable, $parentProperty) : string
    {
        $c1 = $this->qng->generateJoinAlias('c');
        $c2 = $this->qng->generateJoinAlias('c');
        $ch1 = $this->qng->generateJoinAlias('ch');
        $ch2 = $this->qng->generateJoinAlias('ch');
        $i1 = $this->qng->generateJoinAlias('i');

        $sqlVisibilityChild = '';
        $sqlVisibilityItem = '';
        if ($this->em->getFilters()->isEnabled('visibility')) {
            $sqlVisibilityChild = sprintf("WHERE %s.visibility = '%s'", $ch2, VisibilityEnum::VISIBILITY_PUBLIC);
            $sqlVisibilityItem = sprintf("AND %s.visibility = '%s'", $i1, VisibilityEnum::VISIBILITY_PUBLIC);
        };

        return "
            SELECT count(*)
                FROM $itemTable $i1
                WHERE $parentProperty in (
                    WITH RECURSIVE children AS (
                        SELECT $c1.id, $c1.parent_id, $c1.visibility
                        FROM $table $c1
                        WHERE $c1.id = $alias.id
                        UNION
                        SELECT $c2.id, $c2.parent_id, $c2.visibility
                        FROM $table $c2
                        INNER JOIN children $ch1 ON $ch1.id = $c2.parent_id
                  ) SELECT id FROM children $ch2 $sqlVisibilityChild
                )
                $sqlVisibilityItem
        ";
    }
}
