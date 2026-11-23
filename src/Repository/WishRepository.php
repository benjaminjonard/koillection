<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Wish;
use App\Entity\Wishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wish::class);
    }

    public function findNextPosition(Wishlist $wishlist): int
    {
        $maxPosition = $this->createQueryBuilder('w')
            ->select('MAX(w.position)')
            ->where('w.wishlist = :wishlist')
            ->setParameter('wishlist', $wishlist->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $maxPosition + 1;
    }

    public function countForCachedValues(array $ids): array
    {
        return $this
            ->createQueryBuilder('wish')
            ->select('IDENTITY(wish.wishlist) AS id, wish.finalVisibility AS visibility, COUNT(wish.id) AS total')
            ->where('wish.wishlist IN (:ids)')
            ->groupBy('wish.wishlist, wish.finalVisibility')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
