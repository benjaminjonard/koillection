<?php

namespace App\Enum;

/**
 * Class RoleEnum
 *
 * @package App\Enum
 */
class RoleEnum
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];

    const ROLES_TRANS_KEYS = [
        self::ROLE_USER => 'role.user',
        self::ROLE_ADMIN => 'role.admin'
    ];

    /**
     * @param string $type
     * @return string
     */
    public static function getRoleLabel(string $role) : string
    {
        return self::ROLES_TRANS_KEYS[$role];
    }
}
