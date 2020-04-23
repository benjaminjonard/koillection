<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Entity\User;
use App\Model\Search;
use Doctrine\ORM\EntityRepository;

class TagCategoryRepository extends EntityRepository
{
    public function findOneWithTags(string $id) : ?TagCategory
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
