<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\Wishlist;
use App\Enum\DatumTypeEnum;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\RouterInterface;

class Autocompleter
{
    public function __construct(
        private readonly ContextHandler $contextHandler,
        private readonly ManagerRegistry $managerRegistry,
        private readonly RouterInterface $router,
        private readonly FeatureChecker $featureChecker,
        private readonly Security $security
    ) {
    }

    public function findForAutocomplete(string $term): array
    {
        $term = SearchNormalizer::normalize($term);
        $params = [];
        $queryParts = [];
        $queryParts[] = $this->buildRequestForGivenTable($this->managerRegistry->getManager()->getClassMetadata(Collection::class)->getTableName(), 'title', $term, 'collection', $params, 'final_visibility');

        if ($this->security->getUser()?->isSearchInDataByDefaultEnabled()) {
            $queryParts[] = $this->buildRequestForItemTableWithData($term, $params);
        } else {
            $queryParts[] = $this->buildRequestForItemTableWithoutData($term, $params);
        }

        if ($this->featureChecker->isFeatureEnabled('tags')) {
            $queryParts[] = $this->buildRequestForGivenTable($this->managerRegistry->getManager()->getClassMetadata(Tag::class)->getTableName(), 'label', $term, 'tag', $params, 'visibility');
        }

        if ($this->featureChecker->isFeatureEnabled('albums')) {
            $queryParts[] = $this->buildRequestForGivenTable($this->managerRegistry->getManager()->getClassMetadata(Album::class)->getTableName(), 'title', $term, 'album', $params, 'final_visibility');
        }

        if ($this->featureChecker->isFeatureEnabled('wishlists')) {
            $queryParts[] = $this->buildRequestForGivenTable($this->managerRegistry->getManager()->getClassMetadata(Wishlist::class)->getTableName(), 'name', $term, 'wishlist', $params, 'final_visibility');
        }

        $sql = implode(' UNION ', $queryParts);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('counter', 'counter');

        $counterSql = "SELECT COUNT(*) AS counter FROM ({$sql}) AS x";
        $query = $this->managerRegistry->getManager()->createNativeQuery($counterSql, $rsm);
        foreach ($params as $key => $value) {
            $query->setParameter($key + 1, $value);
        }

        $counter = $query->getSingleScalarResult();

        // Get the 5 most relevant results
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('label', 'label');
        $rsm->addScalarResult('type', 'type');
        $sql .= '
            ORDER BY relevance DESC, seenCounter DESC, label ASC
            LIMIT 5
        ';
        $query = $this->managerRegistry->getManager()->createNativeQuery($sql, $rsm);
        foreach ($params as $key => $value) {
            $query->setParameter($key + 1, $value);
        }

        $results = $query->getResult();
        $results = array_map(function ($result) {
            $route = $this->router->generate("app_{$result['type']}_show", ['id' => $result['id']]);
            $result['url'] = $this->contextHandler->getRouteContext($route);
            unset($result['id']);

            return $result;
        }, $results);

        return [
            'results' => $results,
            'totalResultsCounter' => $counter,
        ];
    }

    private function buildRequestForGivenTable(string $collectionTable, string $labelProperty, string $term, string $type, array &$params, string $visibilityColumn): string
    {
        $user = $this->contextHandler->getContextUser();
        $terms = explode(' ', $term);
        $term = implode('%', $terms);
        $normalizedLabelProperty = "normalized_{$labelProperty}";

        $sql = "
            SELECT id AS id, {$labelProperty} AS label, '{$type}' AS type, seen_counter AS seenCounter,
                (CASE
                     WHEN {$normalizedLabelProperty} = ? THEN 4 -- exact match
                     WHEN {$normalizedLabelProperty} LIKE ? THEN 3 -- contains
                     ELSE 0
                END) AS relevance
            FROM {$collectionTable}
            WHERE owner_id = ?
            AND {$normalizedLabelProperty} LIKE ?
        ";

        $params[] = $term;
        $params[] = '%' . $term . '%';
        $params[] = $user->getId();
        $params[] = '%' . $term . '%';

        if ($this->managerRegistry->getManager()->getFilters()->isEnabled('visibility')) {
            $sql .= " AND {$visibilityColumn} IN (?)";
            $params[] = $this->managerRegistry->getManager()->getFilters()->getFilter('visibility')->getVisibilities();
        }

        return $sql;
    }

    private function buildRequestForItemTableWithData(string $term, array &$params): string
    {
        $user = $this->contextHandler->getContextUser();
        $terms = explode(' ', $term);
        $term = implode('%', $terms);
        $itemTable = $this->managerRegistry->getManager()->getClassMetadata(Item::class)->getTableName();
        $datumTable = $this->managerRegistry->getManager()->getClassMetadata(Datum::class)->getTableName();
        $isVisibilityFilterEnabled = $this->managerRegistry->getManager()->getFilters()->isEnabled('visibility');
        $datumVisibilityCondition = $isVisibilityFilterEnabled ? ' AND d.final_visibility IN (?)' : '';

        $sql = "
            SELECT i.id AS id, i.name AS label, 'item' AS type, i.seen_counter AS seenCounter,
                (CASE
                     WHEN i.normalized_name = ? THEN 4 -- item exact match
                     WHEN i.normalized_name LIKE ? THEN 3 -- item end with
                     WHEN d.normalized_value = ? THEN 2 -- datum exact match
                     WHEN d.normalized_value LIKE ? THEN 1 -- datum contains
                     ELSE 0
                END) AS relevance
            FROM {$itemTable} i
            LEFT JOIN {$datumTable} d ON d.item_id = i.id AND d.type IN (?){$datumVisibilityCondition}
            WHERE i.owner_id = ?
            AND (i.normalized_name LIKE ? OR d.normalized_value LIKE ?)
        ";

        $params[] = $term;
        $params[] = '%' . $term . '%';
        $params[] = $term;
        $params[] = '%' . $term . '%';
        $params[] = DatumTypeEnum::AVAILABLE_FOR_SEARCH;
        if ($isVisibilityFilterEnabled) {
            $params[] = $this->managerRegistry->getManager()->getFilters()->getFilter('visibility')->getVisibilities();
        }
        $params[] = $user->getId();
        $params[] = '%' . $term . '%';
        $params[] = '%' . $term . '%';

        if ($isVisibilityFilterEnabled) {
            $sql .= ' AND i.final_visibility IN (?)';
            $params[] = $this->managerRegistry->getManager()->getFilters()->getFilter('visibility')->getVisibilities();
        }

        return $sql;
    }

    private function buildRequestForItemTableWithoutData(string $term, array &$params): string
    {
        $user = $this->contextHandler->getContextUser();
        $terms = explode(' ', $term);
        $term = implode('%', $terms);
        $itemTable = $this->managerRegistry->getManager()->getClassMetadata(Item::class)->getTableName();

        $sql = "
            SELECT i.id AS id, i.name AS label, 'item' AS type, i.seen_counter AS seenCounter,
                (CASE
                     WHEN i.normalized_name = ? THEN 2 -- item exact match
                     WHEN i.normalized_name LIKE ? THEN 1 -- item end with
                     ELSE 0
                END) AS relevance
            FROM {$itemTable} i
            WHERE i.owner_id = ?
            AND i.normalized_name LIKE ?
        ";

        $params[] = $term;
        $params[] = '%' . $term . '%';
        $params[] = $user->getId();
        $params[] = '%' . $term . '%';

        if ($this->managerRegistry->getManager()->getFilters()->isEnabled('visibility')) {
            $sql .= ' AND i.final_visibility IN (?)';
            $params[] = $this->managerRegistry->getManager()->getFilters()->getFilter('visibility')->getVisibilities();
        }

        return $sql;
    }
}
