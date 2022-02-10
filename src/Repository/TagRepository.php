<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Model\Search\Search;
use App\Model\Search\SearchTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findWithItems(string $id) : ?Tag
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.items', 'i')
            ->addSelect('partial i.{id, name, image, imageSmallThumbnail}')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAll() : array
    {
        return $this
            ->createQueryBuilder('t')
            ->orderBy('t.label', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findForTagSearch(SearchTag $search, string $context, int $itemsCount) : array
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT t as tag')
            ->addSelect('count(DISTINCT i.id) as itemCount')
            ->addSelect('(count(DISTINCT i.id)*100.0/:totalItems) as percent')
            ->from(Tag::class, 't')
            ->leftJoin('t.items', 'i')
            ->groupBy('t.id')
            ->orderBy('itemCount', 'DESC')
            ->addOrderBy('t.label', 'ASC')
            ->setFirstResult(($search->getPage() - 1) * $search->getItemsPerPage())
            ->setMaxResults($search->getItemsPerPage())
            ->setParameter('totalItems', $itemsCount > 0 ? $itemsCount : 1)
        ;

        if ($context === 'shared') {
            $qb->having('count(i.id) > 0');
        }

        if (!empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(t.label) LIKE LOWER(:search)')
                ->setParameter('search', '%'.trim($search->getTerm()).'%')
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function countForTagSearch(SearchTag $search, string $context) : int
    {
        $qb = $this->_em
            ->createQueryBuilder()
            ->select('count(DISTINCT t.id)')
            ->from(Tag::class, 't')
        ;

        if ($context === 'shared') {
            $qb
                ->innerJoin('t.items', 'i')
                ->having('count(i.id) > 1')
            ;
        }

        if (!empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(t.label) LIKE LOWER(:search)')
                ->setParameter('search', '%'.trim($search->getTerm()).'%')
            ;
        }

        try {
            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function findLike(string $string) : array
    {
        $string = trim($string);

        return $this
            ->createQueryBuilder('t')
            ->addSelect('(CASE WHEN LOWER(t.label) LIKE LOWER(:startWith) THEN 0 ELSE 1 END) AS HIDDEN startWithOrder')
            ->andWhere('LOWER(t.label) LIKE LOWER(:label)')
            ->orderBy('startWithOrder', 'ASC') //Order tags starting with the search term first
            ->addOrderBy('LOWER(t.label)', 'ASC') //Then order other matching tags alphabetically
            ->setParameter('label', '%'.$string.'%')
            ->setParameter('startWith', $string.'%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findRelatedToCollection(Collection $collection) : array
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.items', 'i')
            ->where('i.collection = :collection')
            ->orderBy('t.label', 'ASC')
            ->groupBy('t.id')
            ->having('count(i.id) =
                (SELECT COUNT(i2.id)
                FROM App\Entity\Item i2
                WHERE i2.collection = :collection)')
            ->setParameter('collection', $collection)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findForSearch(Search $search) : array
    {
        $itemsCount = $this->_em->getRepository(Item::class)->count([]);

        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('t as tag')
            ->addSelect('count(i.id) as itemCount')
            ->addSelect('(count(i.id)*100.0/:totalItems) as percent')
            ->from(Tag::class, 't')
            ->leftJoin('t.items', 'i')
            ->groupBy('t.id')
            ->orderBy('itemCount', 'DESC')
            ->setParameter('totalItems', $itemsCount)
        ;

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(t.label) LIKE LOWER(:term)')
                ->setParameter('term', '%'.$search->getTerm().'%')
            ;
        }

        if ($search->getCreatedAt() instanceof \DateTime) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('t.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllForHighlight()
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('DISTINCT partial t.{id, label}, LENGTH(t.label) as HIDDEN length')
            ->from(Tag::class, 't')
            ->orderBy('length', 'DESC')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findRelatedTags(Tag $tag)
    {
        //Get all items ids the current tag is linked to
        $results = $this->_em->createQueryBuilder()
            ->select('DISTINCT i2.id')
            ->from(Item::class, 'i2')
            ->leftJoin('i2.tags', 't2')
            ->where('t2.id = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getArrayResult()
        ;

        $itemIds = \array_map(function ($row) {
            return $row['id'];
        }, $results);

        return $this->_em
            ->createQueryBuilder()
            ->select('DISTINCT partial t.{id, label}')
            ->from(Tag::class, 't')
            ->leftJoin('t.items', 'i')
            ->where("i.id IN (:itemIds)")
            ->andWhere('t.id != :tag')
            ->orderBy('t.label', 'ASC')
            ->setParameter('tag', $tag)
            ->setParameter('itemIds', $itemIds)
            ->getQuery()
            ->getResult()
        ;
    }
}
