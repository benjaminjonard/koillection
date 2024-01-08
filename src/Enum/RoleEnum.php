<?php

declare(strict_types=1);

namespace App\Enum;

class RoleEnum
{
    public const string ROLE_USER = 'ROLE_USER';

    public const string ROLE_ADMIN = 'ROLE_ADMIN';

    public const array ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];

    public const array ROLES_TRANS_KEYS = [
        self::ROLE_USER => 'role.user',
        self::ROLE_ADMIN => 'role.admin',
    ];

    public static function getRoleLabel(string $role): string
    {
        return self::ROLES_TRANS_KEYS[$role];
    }
}
