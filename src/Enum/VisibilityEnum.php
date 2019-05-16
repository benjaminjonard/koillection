<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Class VisibilityEnum
 *
 * @package App\Enum
 */
class VisibilityEnum
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';

    public const VISIBILITIES = [
        self::VISIBILITY_PUBLIC,
        self::VISIBILITY_PRIVATE
    ];

    public const VISIBILITIES_TRANS_KEYS = [
        self::VISIBILITY_PUBLIC => 'public',
        self::VISIBILITY_PRIVATE => 'private',
    ];

    /**
     * @return array
     */
    public static function getVisibilityLabels() : array
    {
        return [
            self::VISIBILITY_PUBLIC => 'global.visibilities.public',
            self::VISIBILITY_PRIVATE => 'global.visibilities.private',
        ];
    }
}