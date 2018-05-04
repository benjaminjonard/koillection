<?php

namespace App\Enum;

/**
 * Class ThemeEnum
 *
 * @package App\Enum
 */
class ThemeEnum
{
    const THEME_AUBERGINE = 'aubergine';
    const THEME_SUNSET = 'sunset';
    const THEME_TEAL = 'teal';

    const THEMES = [
        self::THEME_AUBERGINE,
        self::THEME_SUNSET,
        self::THEME_TEAL
    ];

    /**
     * @return array
     */
    public static function getThemeLabels() : array
    {
        return self::THEMES;
    }
}
