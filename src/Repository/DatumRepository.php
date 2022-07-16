<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DatumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Datum::class);
    }

    public function findAllLabelsInCollection(Collection $collection): array
    {
        $labels = [];

        $results = $this
            ->createQueryBuilder('datum')
            ->leftJoin('datum.item', 'item')
            ->select('datum.label, datum.type')
            ->distinct()
            ->where('item.collection = :collection')
            ->andWhere('datum.type IN (:types)')
            ->setParameter('collection', $collection)
            ->setParameter('types', [DatumTypeEnum::TYPE_DATE, DatumTypeEnum::TYPE_RATING])
            ->getQuery()
            ->getArrayResult()
        ;

        foreach ($results as $result) {
            $key = $result['label'] . ' (' . $result['type'] . ')';
            $labels[$key] = $result['label'];
        }

        return $labels;
    }
}
