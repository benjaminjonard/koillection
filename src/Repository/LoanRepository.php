<?php

namespace App\Repository;

use App\Entity\Loan;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class LoanRepository
 *
 * @package App\Repository
 */
class LoanRepository extends EntityRepository
{
    /**
     * @return array
     */
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

    /**
     * @return array
     */
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

    /**
     * Find lent item by id with its item.
     *
     * @param $id
     *
     * @return Loan
     */
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
