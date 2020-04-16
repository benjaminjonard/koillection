<?php

declare(strict_types=1);

namespace App\Enum;

class ThemeEnum
{
    public const THEME_AUBERGINE = 'aubergine';
    public const THEME_SUNSET = 'sunset';
    public const THEME_TEAL = 'teal';
    public const THEME_DARK_MODE = 'dark_mode';

    public const THEMES = [
        self::THEME_AUBERGINE,
        self::THEME_SUNSET,
        self::THEME_TEAL,
        self::THEME_DARK_MODE,
    ];

    public const HUE_MAIN = 'main';
    public const HUE_LIGHT = 'light';
    public const HUE_LIGHTEST = 'lightest';
    public const HUE_DARK = 'dark';
    public const HUE_COMPLEMENTARY = 'complementary';

    public const THEME_COLORS = [
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
        ],
        self::THEME_DARK_MODE => [
            'main' => '#42a7ff',
            'lightest' => '#8FF4FF',
            'light' => '#5CC1FF',
            'dark' => '#004199',
            'complementary' => '#f0f0f0'
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
