<?php

namespace App\Enum;

/**
 * Class DatumTypeEnum
 *
 * @package App\Enum
 */
class DatumTypeEnum
{
    const TYPE_TEXT = 'text';
    const TYPE_SIGN = 'sign';
    const TYPE_IMAGE = 'image';

    const TYPES_SLUGS = [
        self::TYPE_TEXT => 'text',
        self::TYPE_SIGN => 'sign',
        self::TYPE_IMAGE => 'image',
    ];

    const TYPES_TRANS_KEYS = [
        self::TYPE_TEXT => 'label.text',
        self::TYPE_SIGN => 'label.sign',
        self::TYPE_IMAGE => 'label.image'
    ];

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

    /**
     * @param string $type
     * @return string
     */
    public static function getTypeSlug(string $type) : string
    {
        return self::TYPES_SLUGS[$type];
    }
}
