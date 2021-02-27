<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Loan;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class LoanRepository extends EntityRepository
{
    public function findLent() : array
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

    public function findReturned() : array
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

    public function findByIdWithItem(string $id) : ?Loan
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
