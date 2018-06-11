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

    const HUE_MAIN = 'main';
    const HUE_LIGHT = 'light';
    const HUE_LIGHTEST = 'lightest';
    const HUE_DARK = 'dark';
    const HUE_COMPLEMENTARY = 'complementary';

    const THEMES = [
        self::THEME_AUBERGINE,
        self::THEME_SUNSET,
        self::THEME_TEAL
    ];

    const THEME_COLORS = [
        self::THEME_AUBERGINE => [
            'main' => '#6c5fc7',
            'lightest' => '#d3bdff',
            'light' => '#9f8dfb',
            'dark' => '#393596',
            'complementary' => '#c79a5f'
        ],
        self::THEME_SUNSET => [
            'main' => '#f44952',
            'lightest' => '#ffb0ad',
            'light' => '#ff7e7e',
            'dark' => '#bb0029',
            'complementary' => '#376891'
        ],
        self::THEME_TEAL => [
            'main' => '#009688',
            'lightest' => '#80cbc4',
            'light' => '#1ab0a2',
            'dark' => '#006355',
            'complementary' => '#E74646'
        ]
    ];

    /**
     * @return array
     */
    public static function getThemeLabels() : array
    {
        return self::THEMES;
    }

    /**
     * @param string $theme
     * @param string $hue
     * @return string
     */
    public static function getThemeColor(string $theme, string $hue) : string
    {
        return self::THEME_COLORS[$theme][$hue];
    }
}
