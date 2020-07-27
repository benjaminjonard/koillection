<?php

declare(strict_types=1);

namespace App\Enum;

class DatumTypeEnum
{
    public const TYPE_TEXT = 'text';
    public const TYPE_SIGN = 'sign';
    public const TYPE_IMAGE = 'image';
    public const TYPE_COUNTRY = 'country';

    public const TYPES_SLUGS = [
        self::TYPE_TEXT => 'text',
        self::TYPE_SIGN => 'sign',
        self::TYPE_IMAGE => 'image',
        self::TYPE_COUNTRY => 'country',
    ];

    public const TYPES_TRANS_KEYS = [
        self::TYPE_TEXT => 'label.text',
        self::TYPE_SIGN => 'label.sign',
        self::TYPE_IMAGE => 'label.image',
        self::TYPE_COUNTRY => 'label.country',
    ];

    /**
     * @return array
     */
    public static function getTextTypesLabels() : array
    {
        return [
            self::TYPE_TEXT => 'label.text',
            self::TYPE_COUNTRY => 'label.country',
        ];
    }

    /**
     * @return array
     */
    public static function getTypesLabels() : array
    {
        return self::TYPES_TRANS_KEYS;
    }

    /**
     * @param string $type
     * @return string
     */
    public static function getTypeLabel(string $type) : string
    {
        return self::TYPES_TRANS_KEYS[$type];
    }
}
