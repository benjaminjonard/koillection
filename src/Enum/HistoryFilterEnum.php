<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Class HistoryFilterEnum
 *
 * @package App\Enum
 */
class HistoryFilterEnum
{
    public const FILTER_COLLECTION = 'collection';
    public const FILTER_ITEM = 'item';
    public const FILTER_TAG = 'tag';

    public const FILTERS = [
        self::FILTER_COLLECTION,
        self::FILTER_ITEM,
        self::FILTER_TAG
    ];
}
