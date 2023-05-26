<?php

declare(strict_types=1);

namespace App\Enum;

class ConfigurationEnum
{
    public const THUMBNAILS_FORMAT = 'thumbnails-format';
    public const CUSTOM_LIGHT_THEME_CSS = 'custom-light-theme-css';
    public const CUSTOM_DARK_THEME_CSS = 'custom-dark-theme-css';

    public const THUMBNAILS_FORMAT_KEEP_ORIGINAL = null;
    public const THUMBNAILS_FORMAT_JPEG = 'jpeg';
    public const THUMBNAILS_FORMAT_PNG = 'png';
    public const THUMBNAILS_FORMAT_WEBP = 'webp';
    public const THUMBNAILS_FORMAT_AVIF = 'avif';

    public const THUMBNAIL_FORMATS = [
        self::THUMBNAILS_FORMAT_KEEP_ORIGINAL,
        self::THUMBNAILS_FORMAT_JPEG,
        self::THUMBNAILS_FORMAT_PNG,
        self::THUMBNAILS_FORMAT_WEBP,
        self::THUMBNAILS_FORMAT_AVIF,
    ];

    public static function getThumbnailFormatsDefaultLabel(): string
    {
        return 'global.configuration.thumbnails_format.keep_original';
    }

    public static function getThumbnailFormatsLabels(): array
    {
        return [
            self::THUMBNAILS_FORMAT_JPEG => 'global.configuration.thumbnails_format.jpeg',
            self::THUMBNAILS_FORMAT_PNG => 'global.configuration.thumbnails_format.png',
            self::THUMBNAILS_FORMAT_WEBP => 'global.configuration.thumbnails_format.webp',
            self::THUMBNAILS_FORMAT_AVIF => 'global.configuration.thumbnails_format.avif',
        ];
    }
}
