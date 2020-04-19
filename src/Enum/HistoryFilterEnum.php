<?php

declare(strict_types=1);

namespace App\Enum;

class HistoryFilterEnum
{
    public const FILTER_CLASS_COLLECTION = 'collection';
    public const FILTER_CLASS_ITEM = 'item';
    public const FILTER_CLASS_TAG = 'tag';

    public const FILTER_TYPE_CREATE = LogTypeEnum::TYPE_CREATE;
    public const FILTER_TYPE_DELETE = LogTypeEnum::TYPE_DELETE;

    public const FILTERS_CLASS = [
        self::FILTER_CLASS_COLLECTION,
        self::FILTER_CLASS_ITEM,
        self::FILTER_CLASS_TAG
    ];

    public const FILTERS_TYPE = [
        self::FILTER_TYPE_CREATE,
        self::FILTER_TYPE_DELETE
    ];

    public const CLASS_TRANS_KEYS = [
        self::FILTER_CLASS_COLLECTION => 'enum.history_filter_class.collection',
        self::FILTER_CLASS_ITEM => 'enum.history_filter_class.item',
        self::FILTER_CLASS_TAG => 'enum.history_filter_class.tag',
    ];

    public const TYPES_TRANS_KEYS = [
        self::FILTER_TYPE_CREATE => 'enum.history_filter_type.create',
        self::FILTER_TYPE_DELETE => 'enum.history_filter_type.delete',
    ];
}
