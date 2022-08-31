<?php

declare(strict_types=1);

namespace App\Enum;

use Doctrine\Common\Collections\Criteria;

class SortingDirectionEnum
{
    public const ASCENDING = Criteria::ASC;
    public const DESCENDING = Criteria::DESC;

    public const SORTING_DIRECTIONS = [
        self::ASCENDING,
        self::DESCENDING,
    ];

    public static function getSortingDirectionLabels(): array
    {
        return [
            self::ASCENDING => 'global.sorting_direction.asc',
            self::DESCENDING => 'global.sorting_direction.desc',
        ];
    }
}
