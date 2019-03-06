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
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';

    const VISIBILITIES = [
        self::VISIBILITY_PUBLIC,
        self::VISIBILITY_PRIVATE
    ];

    const VISIBILITIES_TRANS_KEYS = [
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
