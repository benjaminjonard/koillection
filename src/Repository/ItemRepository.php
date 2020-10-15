<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Model\Search\Search;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;

class ItemRepository extends EntityRepository
{
    /**
     * Find Item by its id.
     *
     * @param $id
     *
     * @throws NonUniqueResultException
     *
     * @return Item
     */
    public function findById(string $id) : ?Item
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.data', 'd')
            ->leftJoin('i.collection', 'c')
            ->leftJoin('i.tags', 't')
            ->where('i.id = :id')
            ->setParameter('id', $id)
        ;

        return $qb->addSelect('t, d, c')->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Item $item
     * @return array
     */
    public function findNextAndPrevious(Item $item, $parent) : array
    {
        $qb = $this->_em
            ->createQueryBuilder()
            ->select('DISTINCT partial i.{id, name}')
            ->from(Item::class, 'i')
        ;

        if ($parent instanceof Collection) {
            $qb
                ->leftJoin('i.collection', 'c')
                ->where('c = :collection')
                ->setParameter('collection', $parent)
            ;
        } elseif ($parent instanceof Tag) {
            $qb
                ->leftJoin('i.tags', 't')
                ->where('t = :tag')
                ->setParameter('tag', $parent)
            ;
        }

        $results = $qb->getQuery()->getArrayResult();

        usort($results, function (array $a, array $b) {
            return strnatcmp($a['name'], $b['name']);
        });

        $count = \count($results);
        $current = null;
        foreach ($results as $key => $result) {
            if ($result['id'] == $item->getId()) {
                $current = $key;
                break;
            }
        }

        if ($current === 0) {
            $previous = null;
            if ($count > 1) {
                $next = $results[($current + 1) % $count];
            } else {
                $next = null;
            }
        } elseif ($current === $count - 1) {
            $previous = $results[($count + $current - 1) % $count];
            $next = null;
        } else {
            $previous = $results[($count + $current - 1) % $count];
            $next = $results[($current + 1) % $count];
        }

        return [
            'previous' => $previous,
            'next' => $next
        ];
    }

    /**
     * @param $search
     * @return array
     */
    public function findForSearch(Search $search) : array
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->orderBy('i.name', 'ASC')
        ;

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
            $qb
                ->andWhere('LOWER(i.name) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $search->getTerm() . '%');
        }

        if ($search->getCreatedAt() instanceof \DateTime) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('i.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Collection $collection
     * @return array
     */
    public function findAllByCollection(Collection $collection) : array
    {
        //First we query all items id recursvely
        $id = "'".$collection->getId()."'";
        $sqlRecursive = "
            SELECT i.id as id
            FROM koi_item i
            WHERE collection_id in (
                WITH RECURSIVE children AS (
                    SELECT c1.id, c1.parent_id, c1.visibility
                    FROM koi_collection c1
                    WHERE c1.id = $id
                    UNION
                    SELECT c2.id, c2.parent_id, c2.visibility
                    FROM koi_collection c2
                    INNER JOIN children ch1 ON ch1.id = c2.parent_id
              ) SELECT id FROM children ch2
            )
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');

        $ids = [];
        foreach ($this->getEntityManager()->createNativeQuery($sqlRecursive, $rsm)->getResult() as $result) {
            $ids[] = $result['id'];
        };

        return $this
            ->createQueryBuilder('i')
            ->select('partial i.{id, name, image, imageSmallThumbnail}')
            ->where('i.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findItemsByCollectionId(string $id) : iterable
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->where('i.collection = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getResult();
    }
}
