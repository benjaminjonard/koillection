<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    public function findLent(): array
    {
        return $this
            ->createQueryBuilder('l')
            ->leftJoin('l.item', 'i')
            ->addSelect('i')
            ->andWhere('l.returnedAt IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findReturned(): array
    {
        return $this
            ->createQueryBuilder('l')
            ->leftJoin('l.item', 'i')
            ->addSelect('i')
            ->andWhere('l.returnedAt IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByIdWithItem(string $id): ?Loan
    {
        return $this
            ->createQueryBuilder('l')
            ->leftJoin('l.item', 'i')
            ->addSelect('i')
            ->where('l.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
