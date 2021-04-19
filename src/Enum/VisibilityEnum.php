<?php

declare(strict_types=1);

namespace App\Enum;

class VisibilityEnum
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_AUTHENTICATED_USERS_ONLY = 'authenticated-users-only';
    public const VISIBILITY_PRIVATE = 'private';

    public const VISIBILITIES = [
        self::VISIBILITY_PUBLIC,
        self::VISIBILITY_AUTHENTICATED_USERS_ONLY,
        self::VISIBILITY_PRIVATE
    ];

    public const VISIBILITIES_TRANS_KEYS = [
        self::VISIBILITY_PUBLIC => 'public',
        self::VISIBILITY_AUTHENTICATED_USERS_ONLY => 'authenticated-users-only',
        self::VISIBILITY_PRIVATE => 'private'
    ];

    public static function getVisibilityLabels() : array
    {
        return [
            self::VISIBILITY_PUBLIC => 'global.visibilities.public',
            self::VISIBILITY_AUTHENTICATED_USERS_ONLY => 'global.visibilities.authenticated_users_only',
            self::VISIBILITY_PRIVATE => 'global.visibilities.private',
        ];
    }
}
