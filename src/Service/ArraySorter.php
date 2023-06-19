<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DisplayConfiguration;
use App\Enum\DatumTypeEnum;
use App\Enum\ReservedLabelEnum;
use App\Enum\SortingDirectionEnum;
use Symfony\Component\Intl\Countries;

class ArraySorter
{
    private readonly ?\Collator $collator;

    public function __construct()
    {
        $this->collator = collator_create('root');
        $this->collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);
    }

    public function sort(
        iterable $array,
        ?DisplayConfiguration $displayConfiguration = null
    ): array {
        $direction = $displayConfiguration instanceof DisplayConfiguration ? $displayConfiguration->getSortingDirection() : SortingDirectionEnum::ASCENDING;
        $type = $displayConfiguration?->getSortingType();
        $property = $displayConfiguration?->getSortingProperty();

        $array = \is_array($array) ? $array : $array->toArray();

        // Sort on name first
        usort($array, function ($a, $b): bool|int {
            return $this->compare($this->getName($a), $this->getName($b));
        });

        switch ($type) {
            case DatumTypeEnum::TYPE_RATING:
            case DatumTypeEnum::TYPE_NUMBER:
            case DatumTypeEnum::TYPE_PRICE:
            case DatumTypeEnum::TYPE_LIST:
            case DatumTypeEnum::TYPE_TEXT:
            case DatumTypeEnum::TYPE_DATE:
                usort($array, function ($a, $b) use ($direction): bool|int {
                    return $this->compare((string) $this->getOrderingValue($a), (string) $this->getOrderingValue($b), $direction);
                });
                break;
            case DatumTypeEnum::TYPE_COUNTRY:
                $countries = Countries::getNames();
                usort($array, function ($a, $b) use ($direction, $countries): bool|int {
                    $a = $countries[$this->getOrderingValue($a)] ?? '';
                    $b = $countries[$this->getOrderingValue($b)] ?? '';

                    return $this->compare($a, $b, $direction);
                });
                break;
            default:
                if (SortingDirectionEnum::DESCENDING == $direction) {
                    $array = array_reverse($array);
                }

                break;
        }

        switch ($property) {
            case ReservedLabelEnum::NUMBER_OF_ITEMS:
                usort($array, function ($a, $b) use ($direction): bool|int {
                    return $this->compare((string) $a->getCachedValues()['counters']['items'], (string) $b->getCachedValues()['counters']['items'], $direction);
                });
                break;
            case ReservedLabelEnum::NUMBER_OF_CHILDREN:
                usort($array, function ($a, $b) use ($direction): bool|int {
                    return $this->compare((string) $a->getCachedValues()['counters']['children'], (string) $b->getCachedValues()['counters']['children'], $direction);
                });
                break;
            default:
                break;
        }

        return $array;
    }

    private function compare($a, $b, $direction = null): bool|int
    {
        if (SortingDirectionEnum::DESCENDING == $direction) {
            return $this->collator->compare($b, $a);
        }

        return $this->collator->compare($a, $b);
    }

    private function getOrderingValue($element)
    {
        return \is_array($element) ? $element['orderingValue'] : $element->getOrderingValue();
    }

    private function getName($element)
    {
        return \is_array($element) ? $element['name'] : $element->__toString();
    }
}
