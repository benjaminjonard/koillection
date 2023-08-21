<?php

declare(strict_types=1);

namespace App\Enum;

class DatumTypeEnum
{
    public const TYPE_TEXT = 'text';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_COUNTRY = 'country';

    public const TYPE_DATE = 'date';

    public const TYPE_RATING = 'rating';

    public const TYPE_NUMBER = 'number';

    public const TYPE_PRICE = 'price';

    public const TYPE_LINK = 'link';

    public const TYPE_LIST = 'list';

    public const TYPE_CHOICE_LIST = 'choice-list';

    public const TYPE_CHECKBOX = 'checkbox';

    public const TYPE_IMAGE = 'image';

    public const TYPE_FILE = 'file';

    public const TYPE_SIGN = 'sign';

    public const TYPES = [
        self::TYPE_TEXT,
        self::TYPE_TEXTAREA,
        self::TYPE_COUNTRY,
        self::TYPE_DATE,
        self::TYPE_RATING,
        self::TYPE_NUMBER,
        self::TYPE_LINK,
        self::TYPE_LIST,
        self::TYPE_CHOICE_LIST,
        self::TYPE_CHECKBOX,
        self::TYPE_IMAGE,
        self::TYPE_FILE,
        self::TYPE_SIGN,
    ];

    public const TEXT_TYPES = [
        self::TYPE_TEXT,
        self::TYPE_TEXTAREA,
        self::TYPE_COUNTRY,
        self::TYPE_DATE,
        self::TYPE_RATING,
        self::TYPE_NUMBER,
        self::TYPE_PRICE,
        self::TYPE_LINK,
        self::TYPE_FILE,
        self::TYPE_LIST,
        self::TYPE_CHOICE_LIST,
        self::TYPE_CHECKBOX,
    ];

    public const IMAGE_TYPES = [
        self::TYPE_IMAGE,
        self::TYPE_SIGN,
    ];

    public const AVAILABLE_FOR_SCRAPING = [
        self::TYPE_TEXT,
        self::TYPE_LIST,
        self::TYPE_COUNTRY,
    ];

    public const AVAILABLE_FOR_SEARCH = [
        self::TYPE_TEXT,
        self::TYPE_LIST,
        self::TYPE_CHOICE_LIST,
        self::TYPE_TEXTAREA,
    ];

    public const TYPES_TRANS_KEYS = [
        self::TYPE_TEXT => 'label.text',
        self::TYPE_TEXTAREA => 'label.textarea',
        self::TYPE_SIGN => 'label.sign',
        self::TYPE_IMAGE => 'label.image',
        self::TYPE_COUNTRY => 'label.country',
        self::TYPE_FILE => 'label.file',
        self::TYPE_DATE => 'label.date',
        self::TYPE_RATING => 'label.rating',
        self::TYPE_NUMBER => 'label.number',
        self::TYPE_PRICE => 'label.price',
        self::TYPE_LINK => 'label.link',
        self::TYPE_LIST => 'label.list',
        self::TYPE_CHOICE_LIST => 'label.choice_list',
        self::TYPE_CHECKBOX => 'label.checkbox'
    ];

    public static function getTypesLabels(): array
    {
        return self::TYPES_TRANS_KEYS;
    }

    public static function getTypeLabel(string $type): string
    {
        return self::TYPES_TRANS_KEYS[$type];
    }
}
