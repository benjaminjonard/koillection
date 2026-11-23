<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class DatumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Datum::class);
    }

    public function findAllItemsLabelsByType(array $types = []): array
    {
        return $this
            ->createQueryBuilder('datum')
            ->leftJoin('datum.item', 'item')
            ->select('datum.label, datum.type')
            ->distinct()
            ->andWhere('datum.type IN (:types)')
            ->setParameter('types', $types)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findAllItemsLabelsInCollection(Collection $collection, array $types = []): array
    {
        return $this
            ->createQueryBuilder('datum')
            ->leftJoin('datum.item', 'item')
            ->select('datum.label, datum.type')
            ->distinct()
            ->where('item.collection = :collection')
            ->andWhere('datum.type IN (:types)')
            ->setParameter('collection', $collection->getId())
            ->setParameter('types', $types)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findAllChildrenLabelsInCollection(?Collection $collection, array $types = []): array
    {
        $qb = $this
            ->createQueryBuilder('datum')
            ->select('datum.label, datum.type')
            ->distinct()
            ->where('datum.type IN (:types)')
            ->setParameter('types', $types)
        ;

        if ($collection instanceof Collection) {
            $qb
                ->join('datum.collection', 'collection', 'WITH', 'collection.parent = :parent')
                ->setParameter('parent', $collection->getId())
            ;
        } else {
            $qb
                ->join('datum.collection', 'collection', 'WITH', 'collection.parent IS NULL')
            ;
        }

        return $qb->getQuery()->getArrayResult();
    }

    public function sumPricesForCachedValues(array $collectionIds): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('collection_id', 'collectionId');
        $rsm->addScalarResult('visibility', 'visibility');
        $rsm->addScalarResult('label', 'label');
        $rsm->addScalarResult('currency', 'currency');
        $rsm->addScalarResult('total', 'total');

        [$regexOperator, $numericType] = match (true) {
            $this->getEntityManager()->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform => ['~', 'NUMERIC'],
            default => ['REGEXP', 'DECIMAL(65,30)'],
        };

        $sql = "
            SELECT item.collection_id AS collection_id, datum.final_visibility AS visibility, datum.label AS label, datum.currency AS currency,
                SUM(CASE WHEN datum.value {$regexOperator} '^([0-9]+|[0-9]*[.][0-9]+)$' THEN CAST(datum.value AS {$numericType}) ELSE 0 END * item.quantity) AS total
            FROM koi_datum datum
            JOIN koi_item item ON datum.item_id = item.id
            WHERE item.collection_id IN (:collectionIds)
            AND datum.type = :type
            GROUP BY item.collection_id, datum.final_visibility, datum.label, datum.currency
        ";

        return $this->getEntityManager()->createNativeQuery($sql, $rsm)
            ->setParameter('collectionIds', $collectionIds, ArrayParameterType::STRING)
            ->setParameter('type', DatumTypeEnum::TYPE_PRICE)
            ->getArrayResult()
        ;
    }

    public function findAllUniqueLabels()
    {
        return $this
            ->createQueryBuilder('datum')
            ->select('datum.label, datum.type')
            ->where("datum.type NOT IN ('price')")
            ->andWhere('datum.item IS NOT NULL')
            ->addGroupBy('datum.label')
            ->addGroupBy('datum.type')
            ->addOrderBy('datum.label')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllUniqueListValues(string $label, string $type): array
    {
        $values = [];

        $results = $this
            ->createQueryBuilder('datum')
            ->select('datum.value')
            ->where("datum.type = :type")
            ->andWhere('datum.label = :label')
            ->andWhere('datum.item IS NOT NULL')
            ->setParameter('type', $type)
            ->setParameter('label', $label)
            ->getQuery()
            ->getResult()
        ;

        foreach ($results as $result) {
            $jsonResult = json_decode($result['value'], true);

            if ($jsonResult !== null) {
                foreach ($jsonResult as $value) {
                    if ($value !== null && !in_array($value, $values)) {
                        $values[] = $value;
                    }
                }
            }

        }

        return $values;
    }
}
