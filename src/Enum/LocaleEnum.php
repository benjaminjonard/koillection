<?php

declare(strict_types=1);

namespace App\Enum;

class LocaleEnum
{
    public const LOCALE_EN_GB = 'en-GB';
    public const LOCALE_FR_FR = 'fr-FR';
    public const LOCALE_ES_ES = 'es-ES';

    public const LOCALES = [
        self::LOCALE_EN_GB => 'en-GB',
        self::LOCALE_FR_FR => 'fr-FR',
        self::LOCALE_ES_ES => 'es-ES',
    ];

    public static function getLocaleLabels(): array
    {
        return [
            self::LOCALE_EN_GB => 'global.locale.en-GB',
            self::LOCALE_FR_FR => 'global.locale.fr-FR',
            self::LOCALE_ES_ES => 'global.locale.es-ES',
        ];
    }
}
