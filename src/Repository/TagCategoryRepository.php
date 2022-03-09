<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TagCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TagCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagCategory::class);
    }

    public function findOneWithTags(string $id): ?TagCategory
    {
        return $this
            ->createQueryBuilder('c')
            ->leftJoin('c.tags', 't')
            ->addSelect('partial t.{id, label}')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
