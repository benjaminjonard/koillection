<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class PhotoRepository extends EntityRepository
{
    public function findPhotosByAlbumId(string $id) : iterable
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->where('p.album = :id')
            ->setParameter('id', $id)
            ->leftJoin('p.image', 'i')
            ->addSelect('partial i.{id, path, thumbnailPath}')
        ;

        return $qb->getQuery()->getResult();
    }
}
