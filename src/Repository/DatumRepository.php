<?php

declare(strict_types=1);

namespace App\Repository;

use App\Enum\DatumTypeEnum;
use Doctrine\ORM\EntityRepository;

class DatumRepository extends EntityRepository
{
    /**
     * @return array
     */
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
