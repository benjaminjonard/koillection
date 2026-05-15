<?php

declare(strict_types=1);

namespace App\Enum;

class ScraperContentTypeEnum
{
    public const string CONTENT_TYPE_HTML = 'html';

    public const string CONTENT_TYPE_JSON = 'json';

    public const array CONTENT_TYPES = [
        self::CONTENT_TYPE_HTML,
        self::CONTENT_TYPE_JSON,
    ];

    public const array CONTENT_TYPE_TRANS_KEYS = [
        self::CONTENT_TYPE_HTML => 'html',
        self::CONTENT_TYPE_JSON => 'json',
    ];

    public static function getContentTypeLabels(): array
    {
        return [
            self::CONTENT_TYPE_HTML => 'enum.scraper_content_type.html',
            self::CONTENT_TYPE_JSON => 'enum.scraper_content_type.json',
        ];
    }
}
