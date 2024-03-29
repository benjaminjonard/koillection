<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Each object has 3 visibility properties :
 * visibility -> the visibility of the object, the only one that can be changed by a user
 * parentVisibility -> the visibility of the object owning the current one
 * finalVisibility -> the visibility used to display or not the object, computed from the 2 previous properties.
 */
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

    public static function computeFinalVisibility(string $visibility, ?string $parentVisibility): string
    {
        if (null === $parentVisibility) {
            return $visibility;
        }

        if (self::VISIBILITY_PUBLIC === $visibility && self::VISIBILITY_PUBLIC === $parentVisibility) {
            return self::VISIBILITY_PUBLIC;
        }

        if (self::VISIBILITY_PRIVATE === $visibility || self::VISIBILITY_PRIVATE === $parentVisibility) {
            return self::VISIBILITY_PRIVATE;
        }

        return self::VISIBILITY_INTERNAL;
    }
}
