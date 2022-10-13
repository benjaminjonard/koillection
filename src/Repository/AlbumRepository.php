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

    public function findAllExcludingItself(Album $album): array
    {
        if (null === $album->getCreatedAt()) {
            return $this->findAll();
        }

        return $this
            ->createQueryBuilder('a')
            ->orderBy('a.title', Criteria::ASC)
            ->where('a != :album')
            ->setParameter('album', $album->getId())
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

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
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
