<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\User;
use App\Model\Search;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class TagRepository extends EntityRepository
{
    private const RESULTS_PER_PAGE = 10;

    public function findById(string $id) : ?Tag
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.image', 'im')
            ->leftJoin('t.items', 'i')
            ->leftJoin('i.image', 'i_i')
            ->addSelect('partial im.{id, path, thumbnailPath}, partial i.{id, name}, partial i_i.{id, thumbnailPath}')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return array
     */
    public function findAll() : array
    {
        return $this
            ->createQueryBuilder('t')
            ->orderBy('t.label', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $itemsCount
     * @param int $page
     * @param string|null $search
     * @param string|null $context
     * @return array
     */
    public function countItemsByTag($itemsCount, $page = 1, string $search = null, string $context = null) : array
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT t as tag')
            ->addSelect('count(DISTINCT i.id) as itemCount')
            ->addSelect('(count(DISTINCT i.id)*100.0/:totalItems) as percent')
            ->from('App\Entity\Tag', 't')
            ->leftJoin('t.items', 'i')
            ->groupBy('t.id')
            ->orderBy('itemCount', 'DESC')
            ->setFirstResult(($page - 1) * self::RESULTS_PER_PAGE)
            ->setMaxResults(self::RESULTS_PER_PAGE)
            ->setParameter('totalItems', $itemsCount > 0 ? $itemsCount : 1)
        ;

        if (\in_array($context, ['user', 'preview'])) {
            $qb->having('count(i.id) > 0');
        }

        if (\is_string($search) && !empty($search)) {
            $qb
                ->andWhere('LOWER(t.label) LIKE LOWER(:search)')
                ->setParameter('search', '%'.trim($search).'%')
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string|null $search
     * @param string|null $context
     * @return int
     * @throws NonUniqueResultException
     */
    public function countTags(string $search = null, string $context = null) : int
    {
        $qb = $this->_em
            ->createQueryBuilder()
            ->select('count(DISTINCT t.id)')
            ->from('App\\Entity\\Tag', 't')
        ;

        if (\in_array($context, ['user', 'preview'])) {
            $qb
                ->innerJoin('t.items', 'i')
                ->having('count(i.id) > 1')
            ;
        }

        if (\is_string($search) && !empty($search)) {
            $qb
                ->andWhere('LOWER(t.label) LIKE LOWER(:search)')
                ->setParameter('search', '%'.trim($search).'%')
            ;
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ? $result[1] : 0;
    }

    /**
     * @param string $string
     * @return array
     */
    public function findLike(string $string) : array
    {
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

    /**
     * Find tags that 100% of a collection items have in common.
     *
     * @param Collection $collection
     *
     * @return array
     */
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

    /**
     * Find for search.
     *
     * @param Search $search
     *
     * @return array
     */
    public function findForSearch(Search $search) : array
    {
        $itemsCount = $this->_em->getRepository(Item::class)->count([]);

        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('t as tag')
            ->addSelect('count(i.id) as itemCount')
            ->addSelect('(count(i.id)*100.0/:totalItems) as percent')
            ->from('App\Entity\Tag', 't')
            ->leftJoin('t.items', 'i')
            ->groupBy('t.id')
            ->orderBy('itemCount', 'DESC')
            ->setParameter('totalItems', $itemsCount)
        ;

        if (\is_string($search->getSearch()) && !empty($search->getSearch())) {
            $qb
                ->andWhere('LOWER(t.label) LIKE LOWER(:search)')
                ->setParameter('search', '%'.$search->getSearch().'%')
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

    /**
     * @param User $owner
     * @return array
     */
    public function findByUserAndWithoutItems(User $owner)
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.items', 'i')
            ->where('i.id IS NULL')
            ->andWhere('t.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array
     */
    public function findAllForHighlight()
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('DISTINCT partial t.{id, label}, LENGTH(t.label) as HIDDEN length')
            ->from('App\\Entity\\Tag', 't')
            ->orderBy('length', 'DESC')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * @param Tag $tag
     * @return array
     */
    public function findRelatedTags(Tag $tag)
    {
        //Get all items ids the current tag is linked to
        $results = $this->_em->createQueryBuilder()
            ->select('DISTINCT i2.id')
            ->from('App\\Entity\\Item', 'i2')
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
            ->from('App\\Entity\\Tag', 't')
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

    /**
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAll() : int
    {
        return $this
            ->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
