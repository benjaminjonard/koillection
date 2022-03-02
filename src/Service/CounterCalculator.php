<?php

declare(strict_types=1);

namespace App\Service;

use App\Doctrine\QueryNameGenerator;
use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class CounterCalculator
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private QueryNameGenerator $qng,
        private ContextHandler $contextHandler
    ) {
    }

    public function computeCounters(): array
    {
        //Collections and items
        $tableName = $this->managerRegistry->getManager()->getClassMetadata(Collection::class)->getTableName();
        $itemTableName = $this->managerRegistry->getManager()->getClassMetadata(Item::class)->getTableName();
        $parentProperty = 'collection_id';
        $globalCacheIndexKey = 'collections';
        $collections = $this->executeItemQuery($tableName, $itemTableName, $parentProperty);
        $collections = array_merge($collections, $this->getGlobalCounters($tableName, $itemTableName, $globalCacheIndexKey));

        //Wishlists and wishes
        $tableName = $this->managerRegistry->getManager()->getClassMetadata(Wishlist::class)->getTableName();
        $itemTableName = $this->managerRegistry->getManager()->getClassMetadata(Wish::class)->getTableName();
        $parentProperty = 'wishlist_id';
        $globalCacheIndexKey = 'wishlists';
        $wishlists = $this->executeItemQuery($tableName, $itemTableName, $parentProperty);
        $wishlists = array_merge($wishlists, $this->getGlobalCounters($tableName, $itemTableName, $globalCacheIndexKey));

        //Albums and photos
        $tableName = $this->managerRegistry->getManager()->getClassMetadata(Album::class)->getTableName();
        $itemTableName = $this->managerRegistry->getManager()->getClassMetadata(Photo::class)->getTableName();
        $parentProperty = 'album_id';
        $globalCacheIndexKey = 'albums';
        $albums = $this->executeItemQuery($tableName, $itemTableName, $parentProperty);
        $albums = array_merge($albums, $this->getGlobalCounters($tableName, $itemTableName, $globalCacheIndexKey));

        return array_merge($collections, $wishlists, $albums);
    }

    public function getGlobalCounters(string $table, string $itemTable, string $cacheIndexName): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('children', 'children');
        $alias = $this->qng->generateJoinAlias('c');
        $ownerId = $this->contextHandler->getContextUser()->getId();
        $results = [];

        $sql = "
            SELECT COUNT(DISTINCT id) as children
            FROM $table $alias
            WHERE $alias.owner_id = '$ownerId'
        ";

        $this->addVisibilityCondition($sql, $alias, 'AND');
        $children = $this->managerRegistry->getManager()->createNativeQuery($sql, $rsm)->getResult()[0]['children'];

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('items', 'items');
        $alias = $this->qng->generateJoinAlias('i');

        $sql = "
            SELECT COUNT(DISTINCT id) as items
            FROM $itemTable $alias
            WHERE $alias.owner_id = '$ownerId'
        ";

        $this->addVisibilityCondition($sql, $alias, 'AND');
        $items = $this->managerRegistry->getManager()->createNativeQuery($sql, $rsm)->getResult()[0]['items'];

        $results[$cacheIndexName] = [
            'children' => $children,
            'items' => $items,
        ];

        return $results;
    }

    public function executeItemQuery(string $table, string $itemTable, string $parentProperty): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addIndexByScalar('id');
        $rsm->addScalarResult('counters', 'counters');
        $alias = $this->qng->generateJoinAlias('c');
        $ownerId = $this->contextHandler->getContextUser()->getId();

        //Counters per objects
        $sqlCounters = $this->getSQLForCounters($alias, $table, $itemTable, $parentProperty);
        $sql = "
            SELECT $alias.id as id, ($sqlCounters) as counters
            FROM $table $alias
            WHERE $alias.owner_id = '$ownerId'
        ";

        $this->addVisibilityCondition($sql, $alias, 'AND');

        $results = [];
        foreach ($this->managerRegistry->getManager()->createNativeQuery($sql, $rsm)->getResult() as $id => $result) {
            $explodedCounters = explode('-', $result['counters']);
            $results[$id] = [
                'children' => $explodedCounters[0],
                'items' => $explodedCounters[1],
            ];
        }

        return $results;
    }

    private function getSQLForCounters(string $alias, string $table, string $itemTable, string $parentProperty): string
    {
        $c1 = $this->qng->generateJoinAlias('c');
        $c2 = $this->qng->generateJoinAlias('c');

        $ch1 = $this->qng->generateJoinAlias('ch');
        $ch2 = $this->qng->generateJoinAlias('ch');

        $i1 = $this->qng->generateJoinAlias('i');
        $i2 = $this->qng->generateJoinAlias('i');

        $visibilityCondition = '';
        $this->addVisibilityCondition($visibilityCondition, $i1, 'AND');

        $sql = "
            WITH RECURSIVE counters AS (
                SELECT $c1.id, $c1.parent_id, $c1.final_visibility, $i1.id AS item_id
                FROM $table $c1
                LEFT JOIN $itemTable $i1 ON $i1.$parentProperty = $c1.id $visibilityCondition
                WHERE $c1.id = $alias.id
                UNION
                SELECT $c2.id, $c2.parent_id, $c2.final_visibility, $i2.id AS item_id 
                FROM $table $c2
                LEFT JOIN $itemTable $i2 ON $i2.$parentProperty = $c2.id
                INNER JOIN counters $ch1 ON $ch1.id = $c2.parent_id
            ) SELECT CONCAT(COUNT(DISTINCT id) - 1, '-' , COUNT(DISTINCT item_id)) FROM counters $ch2
        ";

        $this->addVisibilityCondition($sql, $ch2, 'WHERE');

        return $sql;
    }

    private function addVisibilityCondition(string &$sql, string $alias, string $condition): void
    {
        if ($this->managerRegistry->getManager()->getFilters()->isEnabled('visibility')) {
            if ("''" === $this->managerRegistry->getManager()->getFilters()->getFilter('visibility')->getParameter('user')) {
                $sql .= sprintf("$condition %s.final_visibility = '%s'", $alias, VisibilityEnum::VISIBILITY_PUBLIC);
            } else {
                $sql .= sprintf(
                    "$condition %s.final_visibility IN ('%s', '%s')",
                    $alias,
                    VisibilityEnum::VISIBILITY_PUBLIC,
                    VisibilityEnum::VISIBILITY_INTERNAL
                );
            }
        }
    }
}
