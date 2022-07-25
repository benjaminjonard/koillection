<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\DatumTypeEnum;
use App\Enum\SortingDirectionEnum;
use Symfony\Component\Intl\Countries;

class ArraySorter
{
    public function sortArrays(
        iterable $array,
        ?string $direction = SortingDirectionEnum::ASCENDING,
        ?string $type = null
    ): array {
        $array = !\is_array($array) ? $array->toArray() : $array;

        $collator = collator_create('root');
        $collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);

        // Order alphabetically first, so if two items have the same orderingValue, the two of them will be ordered alphabetically
        usort($array, function ($a, $b) use ($collator) {
            return $collator->compare($a['name'], $b['name']);
        });

        switch ($type) {
            case DatumTypeEnum::TYPE_RATING:
            case DatumTypeEnum::TYPE_NUMBER:
                usort($array, function ($a, $b) use ($direction) {
                    if (SortingDirectionEnum::DESCENDING == $direction) {
                        return (int) $b['orderingValue'] <=> (int) $a['orderingValue'];
                    }

                    return (int) $a['orderingValue'] <=> (int) $b['orderingValue'];
                });
                break;
            case DatumTypeEnum::TYPE_LIST:
            case DatumTypeEnum::TYPE_TEXT:
            case DatumTypeEnum::TYPE_DATE:
                usort($array, function ($a, $b) use ($direction, $collator) {
                    if (SortingDirectionEnum::DESCENDING == $direction) {
                        return $collator->compare((string) $b['orderingValue'], (string) $a['orderingValue']);
                    }

                    return $collator->compare((string) $a['orderingValue'], (string) $b['orderingValue']);
                });
                break;
            case DatumTypeEnum::TYPE_COUNTRY:
                $countries = Countries::getNames();
                usort($array, function ($a, $b) use ($direction, $collator, $countries) {
                    $a = $countries[$a['orderingValue']] ?? '';
                    $b = $countries[$b['orderingValue']] ?? '';

                    if (SortingDirectionEnum::DESCENDING == $direction) {
                        return $collator->compare($b, $a);
                    }

                    return $collator->compare($a, $b);
                });
                break;
            default:
                if (SortingDirectionEnum::DESCENDING == $direction) {
                    $array = array_reverse($array);
                }
                break;
        }

        return $array;
    }

    public function sortObjects(
        iterable $array,
        ?string $direction = SortingDirectionEnum::ASCENDING,
        ?string $type = null
    ): array {
        $array = !\is_array($array) ? $array->toArray() : $array;

        $collator = collator_create('root');
        $collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);

        usort($array, function ($a, $b) use ($collator) {
            return $collator->compare($a->__toString(), $b->__toString());
        });

        switch ($type) {
            case DatumTypeEnum::TYPE_RATING:
            case DatumTypeEnum::TYPE_NUMBER:
                usort($array, function ($a, $b) use ($direction) {
                    if (SortingDirectionEnum::DESCENDING == $direction) {
                        return (int) $b->getOrderingValue() <=> (int) $a->getOrderingValue();
                    }

                    return (int) $a->getOrderingValue() <=> (int) $b->getOrderingValue();
                });
                break;
            case DatumTypeEnum::TYPE_LIST:
            case DatumTypeEnum::TYPE_TEXT:
            case DatumTypeEnum::TYPE_DATE:
                usort($array, function ($a, $b) use ($direction, $collator) {
                    if (SortingDirectionEnum::DESCENDING == $direction) {
                        return $collator->compare((string) $b->getOrderingValue(), (string) $a->getOrderingValue());
                    }

                    return $collator->compare((string) $a->getOrderingValue(), (string) $b->getOrderingValue());
                });
                break;
            case DatumTypeEnum::TYPE_COUNTRY:
                $countries = Countries::getNames();
                usort($array, function ($a, $b) use ($direction, $collator, $countries) {
                    $a = $countries[$a->getOrderingValue()] ?? '';
                    $b = $countries[$b->getOrderingValue()] ?? '';

                    if (SortingDirectionEnum::DESCENDING == $direction) {
                        return $collator->compare($b, $a);
                    }

                    return $collator->compare($a, $b);
                });
                break;
            default:
                if (SortingDirectionEnum::DESCENDING == $direction) {
                    $array = array_reverse($array);
                }
                break;
        }

        return $array;
    }
}
