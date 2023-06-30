<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Album;
use App\Model\Search\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function findAllExcludingItselfAndChildren(Album $album): array
    {
        if (!$album->getCreatedAt() instanceof \DateTimeImmutable) {
            return $this->findAll();
        }

        $excludedAlbums = $album->getChildrenRecursively();
        $excludedAlbums[] = $album->getId();

        return $this
            ->createQueryBuilder('a')
            ->orderBy('a.title', Criteria::ASC)
            ->where('a NOT IN (:excludedAlbums)')
            ->setParameter('excludedAlbums', $excludedAlbums)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findForSearch(Search $search): array
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->orderBy('a.title', Criteria::ASC)
        ;

        if (\is_string($search->getTerm()) && $search->getTerm() !== '') {
            $qb
                ->andWhere('LOWER(a.title) LIKE LOWER(:term)')
                ->setParameter('term', '%'.$search->getTerm().'%')
            ;
        }

        if ($search->getCreatedAt() instanceof \DateTimeImmutable) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('a.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
