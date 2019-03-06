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
    const FILTER_COLLECTION = 'collection';
    const FILTER_ITEM = 'item';
    const FILTER_TAG = 'tag';

    const FILTERS = [
        self::FILTER_COLLECTION,
        self::FILTER_ITEM,
        self::FILTER_TAG
    ];
}
