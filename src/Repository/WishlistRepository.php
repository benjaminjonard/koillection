<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\Wishlist;
use App\Model\Search\Search;
use App\Service\SearchNormalizer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

class WishlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishlist::class);
    }

    #[\Override]
    public function findAll(): array
    {
        return $this
            ->createQueryBuilder('w')
            ->orderBy('w.name', Order::Ascending->value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllExcludingItselfAndChildren(Wishlist $wishlist): array
    {
        if (!$wishlist->getCreatedAt() instanceof \DateTimeImmutable) {
            return $this->findAll();
        }

        $excludedWishlists = $wishlist->getChildrenRecursively();
        $excludedWishlists[] = $wishlist->getId();

        return $this
            ->createQueryBuilder('w')
            ->orderBy('w.name', Order::Ascending->value)
            ->where('w NOT IN (:excludedWishlists)')
            ->setParameter('excludedWishlists', $excludedWishlists)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findForSearch(Search $search): array
    {
        $qb = $this
            ->createQueryBuilder('w')
            ->orderBy('w.name', Order::Ascending->value)
        ;

        if (\is_string($search->getTerm()) && $search->getTerm() !== '') {
            $qb
                ->andWhere('w.normalizedName LIKE :term')
                ->setParameter('term', '%' . SearchNormalizer::normalize($search->getTerm()) . '%')
            ;
        }

        if ($search->getCreatedAt() instanceof \DateTimeImmutable) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('w.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function findTreeForCachedValues(User $owner): array
    {
        return $this
            ->createQueryBuilder('wishlist')
            ->select('wishlist.id AS id, IDENTITY(wishlist.parent) AS parentId, wishlist.finalVisibility AS visibility')
            ->where('wishlist.owner = :owner')
            ->setParameter('owner', $owner->getId())
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
