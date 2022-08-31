<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\User;
use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
use App\Model\Search\Search;
use App\Service\ArraySorter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,
        private readonly ArraySorter $arraySorter
    ) {
        parent::__construct($registry, Item::class);
    }

    public function findById(string $id): ?Item
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

    public function findNextAndPrevious(Item $item, Collection|Tag|null $parent): array
    {
        $results = [];
        if ($parent instanceof Collection) {
            $results = $this->findForOrdering($parent, true);
        } elseif ($parent instanceof Tag) {
            $results = $this->_em
                ->createQueryBuilder()
                ->select('DISTINCT i')
                ->from(Item::class, 'i')
                ->leftJoin('i.tags', 't')
                ->where('t = :tag')
                ->setParameter('tag', $parent->getId())
                ->getQuery()->getArrayResult()
            ;
        }

        $sortDirection = $parent instanceof Collection ? $parent->getItemsSortingDirection() : null;
        $sortType = $parent instanceof Collection ? $parent->getItemsSortingType() : null;
        $results = $this->arraySorter->sort($results, $sortDirection, $sortType);

        $count = \count($results);
        $current = null;
        foreach ($results as $key => $result) {
            if ($result['id'] == $item->getId()) {
                $current = $key;
                break;
            }
        }

        if (0 === $current) {
            $previous = null;
            $next = $count > 1 ? $results[(1) % $count] : null;
        } elseif ($current === $count - 1) {
            $previous = $results[($count + $current - 1) % $count];
            $next = null;
        } else {
            $previous = $results[($count + $current - 1) % $count];
            $next = $results[($current + 1) % $count];
        }

        return [
            'previous' => $previous,
            'next' => $next,
        ];
    }

    public function findForSearch(Search $search): array
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->orderBy('i.name', Criteria::ASC)
        ;

        if (\is_string($search->getTerm()) && !empty($search->getTerm())) {
            $qb
                ->leftJoin('i.data', 'd', 'WITH', 'd.type = :type')
                ->andWhere('LOWER(i.name) LIKE LOWER(:term) OR LOWER(d.value) LIKE LOWER(:term)')
                ->setParameter('type', DatumTypeEnum::TYPE_TEXT)
                ->setParameter('term', '%'.$search->getTerm().'%');
        }

        if ($search->getCreatedAt() instanceof \DateTimeImmutable) {
            $createdAt = $search->getCreatedAt();
            $qb
                ->andWhere('i.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $createdAt->setTime(0, 0, 0))
                ->setParameter('end', $createdAt->setTime(23, 59, 59))
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllByCollection(Collection $collection): array
    {
        // First we query all items id recursvely
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
        }

        $qb = $this
            ->createQueryBuilder('i')
            ->select('partial i.{id, name, image, imageSmallThumbnail, finalVisibility}')
            ->where('i.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        if (DisplayModeEnum::DISPLAY_MODE_LIST === $collection->getItemsDisplayMode()) {
            $qb
                ->leftJoin('i.data', 'data', 'WITH', 'data.label IN (:labels) OR data IS NULL')
                ->addSelect('partial data.{id, label, type, value}')
                ->setParameter('labels', $collection->getItemsDisplayModeListColumns())
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function findItemsByCollectionId(string $id): iterable
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->where('i.collection = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findLike(string $string): array
    {
        $string = trim($string);

        return $this
            ->createQueryBuilder('i')
            ->addSelect('(CASE WHEN LOWER(i.name) LIKE LOWER(:startWith) THEN 0 ELSE 1 END) AS HIDDEN startWithOrder')
            ->andWhere('LOWER(i.name) LIKE LOWER(:name)')
            ->orderBy('startWithOrder', Criteria::ASC) // Order items starting with the search term first
            ->addOrderBy('LOWER(i.name)', Criteria::ASC) // Then order other matching items alphabetically
            ->setParameter('name', '%'.$string.'%')
            ->setParameter('startWith', $string.'%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWithSigns(): array
    {
        return $this
            ->createQueryBuilder('i')
            ->leftJoin('i.data', 'd')
            ->addSelect('d')
            ->andWhere('d.type = :type')
            ->orderBy('i.name', Criteria::ASC)
            ->setParameter('type', DatumTypeEnum::TYPE_SIGN)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneWithRelatedItemsByUser(User $user)
    {
        return $this
            ->createQueryBuilder('i')
            ->join('i.relatedItems', 'r')
            ->addSelect('r')
            ->where('i.owner = :user')
            ->setParameter('user', $user->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findForOrdering(Collection $collection, bool $asArray = false)
    {
        if ($collection->getItemsSortingProperty()) {
            // Get ordering value
            $subQuery = $this->_em
                ->createQueryBuilder()
                ->select('datum.value')
                ->from(Datum::class, 'datum')
                ->where('datum.item = item')
                ->andWhere('datum.label = :label')
                ->andWhere('datum.type IN (:types)')
                ->setMaxResults(1)
            ;

            $qb = $this
                ->createQueryBuilder('item')
                ->addSelect("($subQuery) AS orderingValue, data")
                ->where('item.collection = :collection')
                ->setParameter('collection', $collection->getId())
                ->setParameter('label', $collection->getItemsSortingProperty())
                ->setParameter('types', DatumTypeEnum::AVAILABLE_FOR_ORDERING)
            ;

            // If list, preload datum used for ordering and in columns
            if (DisplayModeEnum::DISPLAY_MODE_LIST === $collection->getItemsDisplayMode()) {
                $qb
                    ->leftJoin('item.data', 'data', 'WITH', 'data.label = :label OR data.label IN (:labels) OR data IS NULL')
                    ->setParameter('labels', $collection->getItemsDisplayModeListColumns())
                ;
            } else {
                // Else only preload datum used for ordering
                $qb
                    ->leftJoin('item.data', 'data', 'WITH', 'data.label = :label OR data IS NULL')
                ;
            }

            $results = $asArray ? $qb->getQuery()->getArrayResult() : $qb->getQuery()->getResult();

            return array_map(static function ($result) use ($asArray) {
                $item = $result[0];
                if ($asArray) {
                    $item['orderingValue'] = $result['orderingValue'];
                } else {
                    $item->setOrderingValue($result['orderingValue']);
                }
                return $item;
            }, $results);
        }

        $qb = $this
            ->createQueryBuilder('item')
            ->where('item.collection = :collection')
            ->setParameter('collection', $collection->getId())
        ;

        if (DisplayModeEnum::DISPLAY_MODE_LIST === $collection->getItemsDisplayMode()) {
            $qb
                ->addSelect('data')
                ->leftJoin('item.data', 'data', 'WITH', 'data.label IN (:labels) OR data IS NULL')
                ->setParameter('labels', $collection->getItemsDisplayModeListColumns())
            ;
        }

        return $asArray ? $qb->getQuery()->getArrayResult() : $qb->getQuery()->getResult();
    }
}
