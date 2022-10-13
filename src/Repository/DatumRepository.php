<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class DatumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Datum::class);
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

    public function computePricesForCollection(Collection $collection)
    {
        $id = $collection->getId();
        $type = DatumTypeEnum::TYPE_PRICE;
        $cast = match ($this->_em->getConnection()->getDatabasePlatform()->getName()) {
            'postgresql' => 'DOUBLE PRECISION',
            'mysql' => 'DECIMAL(12, 2)',
            default => throw new Exception(),
        };

        $rsm = new ResultSetMapping();
        $rsm->addIndexByScalar('label');
        $rsm->addScalarResult('value', 'value');

        $sql = "
            SELECT datum.label AS label, SUM(CAST(datum.value AS {$cast})) AS value
            FROM koi_datum datum
            JOIN koi_item item ON datum.item_id = item.id AND item.collection_id = '{$id}'
            WHERE datum.type = '{$type}'
            GROUP BY datum.label
        ";

        $result = $this->_em->createNativeQuery($sql, $rsm)->getArrayResult();

        return array_map(static function ($price) {
            return $price['value'];
        }, $result);
    }
}
