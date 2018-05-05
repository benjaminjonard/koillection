<?php

namespace App\Enum;

/**
 * Class LocaleEnum
 *
 * @package App\Enum
 */
class LocaleEnum
{
    const LOCALE_EN = 'en';
    const LOCALE_FR = 'fr';

    const LOCALES = [
        self::LOCALE_EN,
        self::LOCALE_FR
    ];

    const LOCALES_TRANS_KEYS = [
        self::LOCALE_EN => 'english',
        self::LOCALE_FR => 'french'
    ];

    /**
     * @return array
     */
    public static function getLocaleLabels() : array
    {
        return self::LOCALES_TRANS_KEYS;
    }
}
