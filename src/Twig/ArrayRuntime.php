<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\SortingDirectionEnum;
use Twig\Extension\RuntimeExtensionInterface;

class ArrayRuntime implements RuntimeExtensionInterface
{
    public function naturalSorting(iterable $array, string $direction = SortingDirectionEnum::ASCENDING, bool $withOrderingValues = false): array
    {
        $array = !\is_array($array) ? $array->toArray() : $array;

        $collator = collator_create('root');
        $collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);

        // Sort by using __toString() function of elements
        $collator->asort($array);

        if ($withOrderingValues) {
            usort($array, function($a, $b) use ($collator, $direction) {
                if ($direction == SortingDirectionEnum::DESCENDING) {
                    return $collator->compare((string) $b->getOrderingValue(), (string) $a->getOrderingValue());
                }
                return $collator->compare((string) $a->getOrderingValue(), (string) $b->getOrderingValue());
            });
        } else {
            if ($direction == SortingDirectionEnum::DESCENDING) {
                $array = array_reverse($array);
            }
        }



        return $array;
    }
}
