<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Tag;
use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
use App\Model\Search\Search;
use App\Service\ArraySorter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
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

        $results = $this->arraySorter->sort($results, $parent->getItemsDisplayConfiguration());

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
            $next = $count > 1 ? $results[1 % $count] : null;
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
        $collectionsIds[] = $collection->getId();
        $parentIds[] = $collection;

        while ($parentIds) {
            $results = $this->_em
                ->createQueryBuilder()
                ->select('c.id')
                ->from(Collection::class, 'c')
                ->where('c.parent in (:parentsIds)')
                ->setParameter('parentsIds', $parentIds)
                ->getQuery()
                ->getArrayResult()
            ;
            $parentIds = array_map(static function (array $result) {
                return $result['id'];
            }, $results);
            $collectionsIds = array_merge($collectionsIds, $parentIds);
        }

        $qb = $this
            ->createQueryBuilder('i')
            ->select('partial i.{id, name, image, imageSmallThumbnail, finalVisibility}')
            ->where('i.collection IN (:collectionsIds)')
            ->setParameter('collectionsIds', $collectionsIds)
        ;

        if (DisplayModeEnum::DISPLAY_MODE_LIST === $collection->getItemsDisplayConfiguration()->getDisplayMode()) {
            $qb
                ->leftJoin('i.data', 'data', 'WITH', 'data.label IN (:labels) OR data IS NULL')
                ->addSelect('partial data.{id, label, type, value}')
                ->setParameter('labels', $collection->getItemsDisplayConfiguration()->getColumns())
            ;
        }

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

    public function findForOrdering(Collection $collection, bool $asArray = false): array
    {
        if ($collection->getItemsDisplayConfiguration()->getSortingProperty()) {
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
                ->addSelect("({$subQuery}) AS orderingValue, data")
                ->where('item.collection = :collection')
                ->setParameter('collection', $collection->getId())
                ->setParameter('label', $collection->getItemsDisplayConfiguration()->getSortingProperty())
                ->setParameter('types', DatumTypeEnum::AVAILABLE_FOR_ORDERING)
            ;

            // If list, preload datum used for ordering and in columns
            if (DisplayModeEnum::DISPLAY_MODE_LIST === $collection->getItemsDisplayConfiguration()->getDisplayMode()) {
                $qb
                    ->leftJoin('item.data', 'data', 'WITH', 'data.label = :label OR data.label IN (:labels) OR data IS NULL')
                    ->setParameter('labels', $collection->getItemsDisplayConfiguration()->getColumns())
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

        if (DisplayModeEnum::DISPLAY_MODE_LIST === $collection->getItemsDisplayConfiguration()->getDisplayMode()) {
            $qb
                ->addSelect('data')
                ->leftJoin('item.data', 'data', 'WITH', 'data.label IN (:labels) OR data IS NULL')
                ->setParameter('labels', $collection->getItemsDisplayConfiguration()->getColumns())
            ;
        }

        return $asArray ? $qb->getQuery()->getArrayResult() : $qb->getQuery()->getResult();
    }
}
