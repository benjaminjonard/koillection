<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\DatumTypeEnum;
use App\Enum\SortingDirectionEnum;
use Twig\Extension\RuntimeExtensionInterface;

class ArrayRuntime implements RuntimeExtensionInterface
{
    public function naturalSorting(
        iterable $array,
        ?string $direction = SortingDirectionEnum::ASCENDING,
        ?string $type = null
    ): array
    {
        $array = !\is_array($array) ? $array->toArray() : $array;

        $collator = collator_create('root');
        $collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);

        // Sort by using __toString() function of elements
        $collator->asort($array);

        switch ($type) {
            case DatumTypeEnum::TYPE_RATING:
            case DatumTypeEnum::TYPE_NUMBER:
                usort($array, function($a, $b) use ($direction) {
                    if ($direction == SortingDirectionEnum::DESCENDING) {
                        return (int) $b->getOrderingValue() <=> (int) $a->getOrderingValue();
                    }

                    return (int) $a->getOrderingValue() <=> (int) $b->getOrderingValue();
                });
                break;
            case DatumTypeEnum::TYPE_DATE:
                usort($array, function($a, $b) use ($direction) {
                    if ($direction == SortingDirectionEnum::DESCENDING) {
                        return new \DateTime($b->getOrderingValue()) <=> new \DateTime($a->getOrderingValue());
                    }

                    return new \DateTime($a->getOrderingValue()) <=> new \DateTime($b->getOrderingValue());
                });
                break;
            default:
                if ($direction == SortingDirectionEnum::DESCENDING) {
                    $array = array_reverse($array);
                }
                break;
        }

        return $array;
    }
}
