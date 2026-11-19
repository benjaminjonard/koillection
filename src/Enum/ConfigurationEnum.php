<?php

declare(strict_types=1);

namespace App\Enum;

class ConfigurationEnum
{
    public const string THUMBNAILS_FORMAT = 'thumbnails-format';

    public const string CUSTOM_LIGHT_THEME_CSS = 'custom-light-theme-css';

    public const string CUSTOM_DARK_THEME_CSS = 'custom-dark-theme-css';

    public const string ENABLE_METRICS = 'enable-metrics';

    public const string APP_NAME = 'app-name';

    public const string APP_LOGO = 'app-logo';

    public const string ENABLE_SCRAPING = 'enable-scraping';

    public const string ENABLE_IMPORT = 'enable-import';

    public const array INSTANCE_FEATURES = [
        'scraping' => self::ENABLE_SCRAPING,
        'import' => self::ENABLE_IMPORT,
    ];

    public const string DEFAULT_APP_NAME = 'Koillection';

    public const array APP_LOGO_SIZES = [16, 32, 48, 72, 96, 128, 144, 168, 192, 256, 512];

    public const null THUMBNAILS_FORMAT_KEEP_ORIGINAL = null;

    public const string THUMBNAILS_FORMAT_JPEG = 'jpeg';

    public const string THUMBNAILS_FORMAT_PNG = 'png';

    public const string THUMBNAILS_FORMAT_WEBP = 'webp';

    public const string THUMBNAILS_FORMAT_AVIF = 'avif';

    public const array THUMBNAIL_FORMATS = [
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
