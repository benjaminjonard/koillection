<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\SortingDirectionEnum;

class NaturalSorter
{
    public function sort(iterable $array, string $direction = SortingDirectionEnum::ASCENDING): array
    {
        $array = !\is_array($array) ? $array->toArray() : $array;

        $collator = collator_create('root');
        $collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);
        $collator->asort($array);

        if ($direction == SortingDirectionEnum::DESCENDING) {
            $array = array_reverse($array);
        }

        return $array;
    }
}
