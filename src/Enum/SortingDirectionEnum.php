<?php

declare(strict_types=1);

namespace App\Enum;

class SortingDirectionEnum
{
    public const ASCENDING = 'asc';
    public const DESCENDING = 'desc';

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
