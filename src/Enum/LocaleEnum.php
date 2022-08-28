<?php

declare(strict_types=1);

namespace App\Enum;

class LocaleEnum
{
    public const LOCALE_EN = 'en';
    public const LOCALE_FR = 'fr';
    public const LOCALE_ES = 'es';

    public const LOCALES = [
        self::LOCALE_EN => 'en',
        self::LOCALE_FR => 'fr',
        self::LOCALE_ES => 'es',
    ];

    public static function getLocaleLabels(): array
    {
        return [
            self::LOCALE_EN => 'global.locale.en',
            self::LOCALE_FR => 'global.locale.fr',
            self::LOCALE_ES => 'global.locale.es',
        ];
    }
}
