<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Search;
use App\Enum\AdvancedItemSearch\ConditionEnum;
use App\Enum\AdvancedItemSearch\OperatorEnum;
use App\Enum\AdvancedItemSearch\TypeEnum;
use App\Enum\DatumTypeEnum;
use App\Repository\ItemRepository;
use Doctrine\ORM\QueryBuilder;

class AdvancedItemSearcher
{
    public function __construct(private readonly ItemRepository $itemRepository)
    {
    }

    public function search(Search $search): array
    {
        $qb = $this->itemRepository
            ->createQueryBuilder('item')
            ->addSelect('loan')
            ->leftJoin('item.loans', 'loan')
        ;

        foreach ($search->getBlocks() as $blockKey => $block) {
            $blockParts = [];
            foreach ($block->getFilters() as $filterKey => $filter) {
                $filterParts = [];
                if ($filter->getCondition()) {
                    $filterParts[] = strtoupper($filter->getCondition());
                }

                $filterParts[] = match ($filter->getType()) {
                    TypeEnum::TYPE_NAME => $this->buildNameFilter($qb, $filter->getOperator(), $filter->getValue()),
                    TypeEnum::TYPE_COLLECTION => $this->buildCollectionFilter($qb, $filter->getOperator(), $filter->getValue()),
                    TypeEnum::TYPE_TAG => $this->buildTagFilter($qb, $filter->getOperator(), $filter->getValue()),
                    TypeEnum::TYPE_DATUM => $this->buildDatumFilter($qb, $filter->getOperator(), $filter->getValue(), $filter->getDatumLabel(), $filter->getDatumType()),
                };

                $blockParts[] = implode(' ', $filterParts);
            }

            $blockSql = implode(' ', $blockParts);

            if (empty($blockSql)) {
                continue;
            }

            match (true) {
                $block->getCondition() === ConditionEnum::CONDITION_AND => $qb->andWhere($blockSql),
                $block->getCondition() === ConditionEnum::CONDITION_OR => $qb->orWhere($blockSql),
            };
        }

        return $qb->getQuery()->getResult();
    }

    private function buildNameFilter(QueryBuilder $queryBuilder, string $operator, string $value): string
    {
        $paramValue = uniqid('name_value_');

        if ($operator === OperatorEnum::OPERATOR_EQUAL) {
            $queryBuilder->setParameter($paramValue, $value);

            return "LOWER(item.name) = LOWER(:{$paramValue})";
        }

        if ($operator === OperatorEnum::OPERATOR_NOT_EQUAL) {
            $queryBuilder->setParameter($paramValue, $value);

            return "LOWER(item.name) != LOWER(:{$paramValue})";
        }

        if ($operator === OperatorEnum::OPERATOR_CONTAINS) {
            $queryBuilder->setParameter($paramValue, "%{$value}%");

            return "LOWER(item.name) LIKE LOWER(:{$paramValue})";
        }

        if ($operator === OperatorEnum::OPERATOR_DOES_NOT_CONTAIN) {
            $queryBuilder->setParameter($paramValue, "%{$value}%");

            return "LOWER(item.name) NOT LIKE LOWER(:{$paramValue})";
        }

        return '';
    }

    private function buildCollectionFilter(QueryBuilder $queryBuilder, string $operator, string $value): string
    {
        $paramValue = uniqid('collection_value_');

        if ($operator === OperatorEnum::OPERATOR_EQUAL) {
            $queryBuilder->setParameter($paramValue, $value);

            return "item.collection = :{$paramValue}";
        }

        if ($operator === OperatorEnum::OPERATOR_NOT_EQUAL) {
            $queryBuilder->setParameter($paramValue, $value);

            return "item.collection != :{$paramValue}";
        }

        return '';
    }

    private function buildTagFilter(QueryBuilder $queryBuilder, string $operator, string $value): string
    {
        $paramValue = uniqid('tag_value_');
        $tagAlias = uniqid('tag_');

        if ($operator === OperatorEnum::OPERATOR_EQUAL) {
            $queryBuilder->leftJoin('item.tags', $tagAlias, 'WITH', "$tagAlias.id = :{$paramValue}");
            $queryBuilder->setParameter($paramValue, $value);

            return "$tagAlias.id IS NOT NULL";
        }

        if ($operator === OperatorEnum::OPERATOR_NOT_EQUAL) {
            $tagSubqueryAlias = uniqid('tag_subquery_');
            $itemSubqueryAlias = uniqid('item_subquery_');

            $sub = $this->itemRepository->createQueryBuilder($itemSubqueryAlias)
                ->select('1')
                ->join("$itemSubqueryAlias.tags", "$tagSubqueryAlias")
                ->where("$itemSubqueryAlias = item")
                ->andWhere("$tagSubqueryAlias.id = :{$paramValue}")
                ->getDQL()
            ;

            $queryBuilder->setParameter($paramValue, $value);

            return "NOT EXISTS ($sub)";
        }

        return '';
    }

    private function buildDatumFilter(QueryBuilder $queryBuilder, string $operator, ?string $value, string $datumLabel, string $datumType): string
    {
        $datumAlias = uniqid('datum_');

        $paramValue = uniqid('datum_value_');

        $paramLabel = uniqid('datum_label_');
        $queryBuilder->setParameter($paramLabel, $datumLabel);

        $paramType = uniqid('datum_type_');
        $queryBuilder->setParameter($paramType, $datumType);

        $cast = match ($datumType) {
            DatumTypeEnum::TYPE_NUMBER, DatumTypeEnum::TYPE_RATING => 'INTEGER',
            DatumTypeEnum::TYPE_DATE => 'DATE',
            default => null
        };

        $sql = '';

        if ($operator === OperatorEnum::OPERATOR_EQUAL) {
            if ($datumType === DatumTypeEnum::TYPE_NUMBER || $datumType === DatumTypeEnum::TYPE_RATING) {
                $queryBuilder->setParameter($paramValue, $value);
                $sql = "CAST($datumAlias.value AS INTEGER) = CAST(:{$paramValue} AS INTEGER)";
            } else {
                $queryBuilder->setParameter($paramValue, $value);
                $sql = "LOWER($datumAlias.value) = LOWER(:{$paramValue})";
            }
        }

        if ($operator === OperatorEnum::OPERATOR_NOT_EQUAL) {
            if ($datumType === DatumTypeEnum::TYPE_NUMBER || $datumType === DatumTypeEnum::TYPE_RATING) {
                $queryBuilder->setParameter($paramValue, $value);
                $sql = "CAST($datumAlias.value AS INTEGER) != CAST(:{$paramValue} AS INTEGER)";
            } else {
                $queryBuilder->setParameter($paramValue, $value);
                $sql = "LOWER($datumAlias.value) != LOWER(:{$paramValue})";
            }
        }

        if ($operator === OperatorEnum::OPERATOR_CONTAINS) {
            if ($datumType === DatumTypeEnum::TYPE_CHOICE_LIST || $datumType === DatumTypeEnum::TYPE_LIST) {
                $queryBuilder->setParameter($paramValue, "%\"{$value}\"%");
                $sql = "LOWER($datumAlias.value) LIKE LOWER(:{$paramValue})";
            } else {
                $queryBuilder->setParameter($paramValue, "%{$value}%");
                $sql = "LOWER($datumAlias.value) LIKE LOWER(:{$paramValue})";
            }
        }

        if ($operator === OperatorEnum::OPERATOR_DOES_NOT_CONTAIN) {
            if ($datumType === DatumTypeEnum::TYPE_CHOICE_LIST || $datumType === DatumTypeEnum::TYPE_LIST) {
                $queryBuilder->setParameter($paramValue, "%\"{$value}\"%");
                $sql = "LOWER($datumAlias.value) NOT LIKE LOWER(:{$paramValue})";
            } else {
                $queryBuilder->setParameter($paramValue, "%{$value}%");
                $sql = "LOWER($datumAlias.value) NOT LIKE LOWER(:{$paramValue})";
            }
        }

        if ($operator === OperatorEnum::OPERATOR_SUPERIOR) {
            $queryBuilder->setParameter($paramValue, $value);
            $sql = "CAST($datumAlias.value AS $cast) > CAST(:{$paramValue} AS $cast)";
        }

        if ($operator === OperatorEnum::OPERATOR_SUPERIOR_OR_EQUAL) {
            $queryBuilder->setParameter($paramValue, $value);
            $sql = "CAST($datumAlias.value AS $cast) >= CAST(:{$paramValue} AS $cast)";
        }

        if ($operator === OperatorEnum::OPERATOR_INFERIOR) {
            $queryBuilder->setParameter($paramValue, $value);
            $sql = "CAST($datumAlias.value AS $cast) < CAST(:{$paramValue} AS $cast)";
        }

        if ($operator === OperatorEnum::OPERATOR_INFERIOR_OR_EQUAL) {
            $queryBuilder->setParameter($paramValue, $value);
            $sql = "CAST($datumAlias.value AS $cast) <= CAST(:{$paramValue} AS $cast)";
        }

        if ($operator === OperatorEnum::OPERATOR_EXISTS) {
            $sql = "$datumAlias.id IS NOT NULL";
        }

        if ($operator === OperatorEnum::OPERATOR_DOES_NOT_EXIST) {
            $sql = "$datumAlias.id IS NULL";
        }

        if ($operator === OperatorEnum::OPERATOR_EMPTY) {
            $property = match ($datumType) {
                DatumTypeEnum::TYPE_IMAGE, DatumTypeEnum::TYPE_SIGN => 'image',
                DatumTypeEnum::TYPE_VIDEO => 'video',
                DatumTypeEnum::TYPE_FILE => 'file',
                default => 'value'
            };
            $sql = "$datumAlias.id IS NOT NULL AND ($datumAlias.$property IS NULL OR $datumAlias.$property = '' OR $datumAlias.$property = '[]')";
        }

        if ($operator === OperatorEnum::OPERATOR_NOT_EMPTY) {
            $property = match ($datumType) {
                DatumTypeEnum::TYPE_IMAGE, DatumTypeEnum::TYPE_SIGN => 'image',
                DatumTypeEnum::TYPE_VIDEO => 'video',
                DatumTypeEnum::TYPE_FILE => 'file',
                default => 'value'
            };
            $sql = "($datumAlias.$property IS NOT NULL AND $datumAlias.$property != '' AND $datumAlias.$property != '[]')";
        }

        $queryBuilder->leftJoin('item.data', $datumAlias, 'WITH', "$datumAlias.label = :{$paramLabel} AND $datumAlias.type = :{$paramType}");

        return $sql;
    }
}
