<?php

namespace App\Enum;

/**
 * Class VisibilityEnum
 *
 * @package App\Enum
 */
class VisibilityEnum
{
    const VISIBILITY_PUBLIC = 1;
    const VISIBILITY_PRIVATE = 2;

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
