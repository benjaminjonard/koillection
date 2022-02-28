<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Log;
use App\Model\Search\SearchHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function findForSearch(SearchHistory $search): array
    {
        $classes = array_map(function ($value) {
            return 'App\Entity\\'.ucfirst($value);
        }, $search->getClasses());

        $qb = $this
            ->createQueryBuilder('l')
            ->where('l.type in (:types)')
            ->andWhere('l.objectClass in (:classes)')
            ->orderBy('l.loggedAt', 'DESC')
            ->setFirstResult(($search->getPage() - 1) * $search->getItemsPerPage())
            ->setMaxResults($search->getItemsPerPage())
            ->setParameter('types', $search->getTypes())
            ->setParameter('classes', $classes)
        ;

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(l.objectLabel) LIKE LOWER(:term)')
                ->setParameter('term', '%'.trim($search->getTerm()).'%')
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function countForSearch(SearchHistory $search): int
    {
        $classes = array_map(function ($value) {
            return 'App\Entity\\'.ucfirst($value);
        }, $search->getClasses());

        $qb = $this->_em
            ->createQueryBuilder()
            ->select('count(DISTINCT l.id)')
            ->where('l.type in (:types)')
            ->andWhere('l.objectClass in (:classes)')
            ->setParameter('types', $search->getTypes())
            ->setParameter('classes', $classes)
            ->from(Log::class, 'l')
        ;

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(l.objectLabel) LIKE LOWER(:term)')
                ->setParameter('term', '%'.trim($search->getTerm()).'%')
            ;
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
