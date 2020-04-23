<?php

declare(strict_types=1);

namespace App\Enum;

class LocaleEnum
{
    public const LOCALE_EN = 'en';
    public const LOCALE_FR = 'fr';

    public const LOCALES = [
        self::LOCALE_EN,
        self::LOCALE_FR
    ];

    public const FULL_LOCALES = [
        self::LOCALE_EN => 'en_GB',
        self::LOCALE_FR => 'fr_FR'
    ];

    public const LOCALES_TRANS_KEYS = [
        self::LOCALE_EN => 'en_GB',
        self::LOCALE_FR => 'fr_FR'
    ];

    /**
     * @return array
     */
    public static function getLocaleLabels() : array
    {
        return [
            self::LOCALE_EN => 'global.locale.en_GB',
            self::LOCALE_FR => 'global.locale.fr_FR',
        ];
    }

    /**
     * @return array
     */
    public static function getFullLocales() : array
    {
        return self::FULL_LOCALES;
    }
}
