<?php

declare(strict_types=1);

namespace App\Enum;

class HistoryFilterEnum
{
    public const FILTER_CLASS_COLLECTION = 'collection';

    public const FILTER_CLASS_ITEM = 'item';

    public const FILTER_CLASS_TEMPLATE = 'template';

    public const FILTER_CLASS_CHOICE_LIST = 'choiceList';

    public const FILTER_CLASS_INVENTORY = 'inventory';

    public const FILTER_CLASS_TAG = 'tag';

    public const FILTER_CLASS_TAG_CATEGORY = 'tagCategory';

    public const FILTER_CLASS_ALBUM = 'album';

    public const FILTER_CLASS_PHOTO = 'photo';

    public const FILTER_CLASS_WISHLIST = 'wishlist';

    public const FILTER_CLASS_WISH = 'wish';

    public const FILTER_TYPE_CREATE = LogTypeEnum::TYPE_CREATE;

    public const FILTER_TYPE_DELETE = LogTypeEnum::TYPE_DELETE;

    public const FILTERS_CLASS = [
        self::FILTER_CLASS_COLLECTION,
        self::FILTER_CLASS_ITEM,
        self::FILTER_CLASS_TEMPLATE,
        self::FILTER_CLASS_INVENTORY,
        self::FILTER_CLASS_CHOICE_LIST,
        self::FILTER_CLASS_TAG,
        self::FILTER_CLASS_TAG_CATEGORY,
        self::FILTER_CLASS_ALBUM,
        self::FILTER_CLASS_PHOTO,
        self::FILTER_CLASS_WISHLIST,
        self::FILTER_CLASS_WISH,
    ];

    public const FILTERS_TYPE = [
        self::FILTER_TYPE_CREATE,
        self::FILTER_TYPE_DELETE,
    ];

    public const CLASS_TRANS_KEYS = [
        self::FILTER_CLASS_COLLECTION => 'enum.history_filter_class.collection',
        self::FILTER_CLASS_ITEM => 'enum.history_filter_class.item',
        self::FILTER_CLASS_TAG => 'enum.history_filter_class.tag',
        self::FILTER_CLASS_ALBUM => 'enum.history_filter_class.album',
        self::FILTER_CLASS_WISHLIST => 'enum.history_filter_class.wishlist',

        self::FILTER_CLASS_TEMPLATE => 'enum.history_filter_class.template',
        self::FILTER_CLASS_INVENTORY => 'enum.history_filter_class.inventory',
        self::FILTER_CLASS_CHOICE_LIST => 'enum.history_filter_class.choice_list',
        self::FILTER_CLASS_TAG_CATEGORY => 'enum.history_filter_class.tag_category',
        self::FILTER_CLASS_PHOTO => 'enum.history_filter_class.photo',
        self::FILTER_CLASS_WISH => 'enum.history_filter_class.wish',
    ];

    public const TYPES_TRANS_KEYS = [
        self::FILTER_TYPE_CREATE => 'enum.history_filter_type.create',
        self::FILTER_TYPE_DELETE => 'enum.history_filter_type.delete',
    ];

    public static function getLabel(string $type): string
    {
        return self::CLASS_TRANS_KEYS[$type];
    }
}
