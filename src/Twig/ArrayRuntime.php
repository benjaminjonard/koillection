<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\SortingDirectionEnum;
use Twig\Extension\RuntimeExtensionInterface;

class ArrayRuntime implements RuntimeExtensionInterface
{
    public function naturalSorting(iterable $array, string $direction = SortingDirectionEnum::ASCENDING): array
    {
        $array = !\is_array($array) ? $array->toArray() : $array;

        $collator = collator_create('root');
        $collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);

        // Sort by using __toString() function of elements
        $collator->asort($array);

        if (isset($array[0]) && $array[0]->getOrderingValue()) {
            usort($array, function($a, $b) use ($collator, $direction) {
                return $collator->compare((string) $a->getOrderingValue(), (string) $b->getOrderingValue());
            });
        }
        
        if ($direction == SortingDirectionEnum::DESCENDING) {
            $array = array_reverse($array);
        }

        return $array;
    }
}
