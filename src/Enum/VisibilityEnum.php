<?php

declare(strict_types=1);

namespace App\Enum;

class VisibilityEnum
{
    public const string VISIBILITY_PUBLIC = 'public';

    public const string VISIBILITY_INTERNAL = 'internal';

    public const string VISIBILITY_PRIVATE = 'private';

    public const array VISIBILITIES = [
        self::VISIBILITY_PUBLIC,
        self::VISIBILITY_INTERNAL,
        self::VISIBILITY_PRIVATE,
    ];

    public const array VISIBILITIES_TRANS_KEYS = [
        self::VISIBILITY_PUBLIC => 'public',
        self::VISIBILITY_INTERNAL => 'internal',
        self::VISIBILITY_PRIVATE => 'private',
    ];

    public static function getVisibilityLabels(): array
    {
        return [
            self::VISIBILITY_PUBLIC => 'global.visibilities.public',
            self::VISIBILITY_INTERNAL => 'global.visibilities.internal',
            self::VISIBILITY_PRIVATE => 'global.visibilities.private',
        ];
    }
}
