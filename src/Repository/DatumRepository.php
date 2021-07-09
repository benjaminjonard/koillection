<?php

declare(strict_types=1);

namespace App\Repository;

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

    public function findSigns() : array
    {
        return $this
            ->createQueryBuilder('d')
            ->leftJoin('d.item', 'i')
            ->addSelect('i')
            ->andWhere('d.type = :type')
            ->orderBy('i.name', 'ASC')
            ->setParameter('type', DatumTypeEnum::TYPE_SIGN)
            ->getQuery()
            ->getResult()
        ;
    }
}
