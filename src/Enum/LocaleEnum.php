<?php

declare(strict_types=1);

namespace App\Enum;

class LocaleEnum
{
    public const LOCALE_EN_GB = 'en_GB';
    public const LOCALE_FR_FR = 'fr_FR';

    public const LOCALES = [
        self::LOCALE_EN_GB => 'en_GB',
        self::LOCALE_FR_FR => 'fr_FR'
    ];

    /**
     * @return array
     */
    public static function getLocaleLabels() : array
    {
        return [
            self::LOCALE_EN_GB => 'global.locale.en_GB',
            self::LOCALE_FR_FR => 'global.locale.fr_FR',
        ];
    }
}
