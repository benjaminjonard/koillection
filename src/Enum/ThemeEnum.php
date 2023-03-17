<?php

declare(strict_types=1);

namespace App\Enum;

class ThemeEnum
{
    public const THEME_BROWSER = 'browser';
    public const THEME_LIGHT = 'light';
    public const THEME_DARK = 'dark';

    public const THEMES = [
        self::THEME_BROWSER,
        self::THEME_LIGHT,
        self::THEME_DARK,
    ];

    public const THEMES_TRANS_KEYS = [
        self::THEME_BROWSER => 'browser',
        self::THEME_LIGHT => 'light',
        self::THEME_DARK => 'dark',
    ];

    public static function getThemesLabels(): array
    {
        return [
            self::THEME_BROWSER => 'global.themes.browser',
            self::THEME_LIGHT => 'global.themes.light',
            self::THEME_DARK => 'global.themes.dark',
        ];
    }
}
