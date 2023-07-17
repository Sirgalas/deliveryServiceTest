<?php

declare(strict_types=1);

namespace App\Framework\Security\Role;

class Role
{

    final public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    final public const ROLE_ADMIN = 'ROLE_ADMIN';

    final public const ROLE_USER = 'ROLE_USER';

    final public const ALL = [

        self::ROLE_SUPER_ADMIN => self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN => self::ROLE_ADMIN,
        self::ROLE_USER => self::ROLE_USER,
    ];

    final public const ALL_TITLE = [

        self::ROLE_SUPER_ADMIN => 'Супер-администратор',
        self::ROLE_ADMIN => 'Администратор',
        self::ROLE_USER => 'Пользователь',
    ];

    final public const ADMIN_ROLES = [

        self::ROLE_SUPER_ADMIN => self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN => self::ROLE_ADMIN,
    ];
}