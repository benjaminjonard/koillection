<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function countForCachedValues(array $ids): array
    {
        return $this
            ->createQueryBuilder('photo')
            ->select('IDENTITY(photo.album) AS id, photo.finalVisibility AS visibility, COUNT(photo.id) AS total')
            ->where('photo.album IN (:ids)')
            ->groupBy('photo.album, photo.finalVisibility')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
