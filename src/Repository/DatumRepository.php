<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Datum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DatumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Datum::class);
    }

    public function findAllLabelsInCollection(Collection $collection, array $types = []): array
    {
        return $this
            ->createQueryBuilder('datum')
            ->leftJoin('datum.item', 'item')
            ->select('datum.label, datum.type')
            ->distinct()
            ->where('item.collection = :collection')
            ->andWhere('datum.type IN (:types)')
            ->setParameter('collection', $collection)
            ->setParameter('types', $types)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
