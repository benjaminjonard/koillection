<?php

declare(strict_types=1);

namespace App\Enum;

use Doctrine\Common\Collections\Order;

class SortingDirectionEnum
{
    public const string ASCENDING = Order::Ascending->value;

    public const string DESCENDING = Order::Descending->value;

    public const array SORTING_DIRECTIONS = [
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
