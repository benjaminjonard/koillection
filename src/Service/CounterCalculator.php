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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * CounterCalculator constructor.
     * @param EntityManagerInterface $em
     * @param QueryNameGenerator $qng
     */
    public function __construct(EntityManagerInterface $em, QueryNameGenerator $qng, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->qng = $qng;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param $object
     * @return array
     */
    public function computeCounters() : array
    {
        $params = [
            $this->em->getClassMetadata(Collection::class)->getTableName(),
            $this->em->getClassMetadata(Item::class)->getTableName(),
            'collection_id'
        ];
        $collections = $this->executeItemQuery(...$params);

        $params = [
            $this->em->getClassMetadata(Wishlist::class)->getTableName(),
            $this->em->getClassMetadata(Wish::class)->getTableName(),
            'wishlist_id'
        ];
        $wishlists = $this->executeItemQuery(...$params);

        return array_merge($collections, $wishlists);
    }

    /**
     * @param $table
     * @param $itemTable
     * @param $parentProperty
     * @return array
     */
    public function executeItemQuery($table, $itemTable, $parentProperty) : array
    {
        $rsm = new ResultSetMapping();
        $rsm->addIndexByScalar('id');
        $rsm->addScalarResult('counters', 'counters');
        $alias = $this->qng->generateJoinAlias('c');
        $ownerId = $this->tokenStorage->getToken()->getUser()->getId();

        $sqlCounters = $this->getSQLForCounters($alias, $table, $itemTable, $parentProperty);
        $sql = "
            SELECT $alias.id as id, ($sqlCounters) as counters 
            FROM $table $alias
            WHERE $alias.owner_id = '$ownerId'
        ";

        if ($this->em->getFilters()->isEnabled('visibility')) {
            $sql .= sprintf("AND %s.visibility = '%s'", $alias, VisibilityEnum::VISIBILITY_PUBLIC);
        };

        $results = [];
        foreach ($this->em->createNativeQuery($sql, $rsm)->getResult() as $id => $result) {
            $explodedCounters = explode('-', $result['counters']);
            $results[$id] = [
                'children' => $explodedCounters[0],
                'items' => $explodedCounters[1],
            ];
        }

        return $results;
    }

    /**
     * @param string $alias
     * @param $table
     * @param $itemTable
     * @return string
     */
    private function getSQLForCounters(string $alias, $table, $itemTable, $parentProperty) : string
    {
        $c1 = $this->qng->generateJoinAlias('c');
        $c2 = $this->qng->generateJoinAlias('c');

        $ch1 = $this->qng->generateJoinAlias('ch');
        $ch2 = $this->qng->generateJoinAlias('ch');

        $i1 = $this->qng->generateJoinAlias('i');
        $i2 = $this->qng->generateJoinAlias('i');

        $sql = "
            WITH RECURSIVE counters AS (
                SELECT $c1.id, $c1.parent_id, $c1.visibility, $i1.id AS item_id
                FROM $table $c1
                LEFT JOIN $itemTable $i1 ON $i1.$parentProperty = $c1.id
                WHERE $c1.id = $alias.id
                UNION
                SELECT $c2.id, $c2.parent_id, $c2.visibility, $i2.id AS item_id 
                FROM $table $c2
                LEFT JOIN $itemTable $i2 ON $i2.$parentProperty = $c2.id
                INNER JOIN counters $ch1 ON $ch1.id = $c2.parent_id
            ) SELECT CONCAT(COUNT(DISTINCT id) - 1, '-' , COUNT(DISTINCT item_id)) FROM counters $ch2
        ";

        if ($this->em->getFilters()->isEnabled('visibility')) {
            $sql .= sprintf("AND %s.visibility = '%s'", $ch2, VisibilityEnum::VISIBILITY_PUBLIC);
        };

        return $sql;
    }
}
