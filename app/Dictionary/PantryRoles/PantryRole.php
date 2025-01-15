<?php

namespace App\Dictionary\PantryRoles;
enum PantryRole: int {
    case CREATOR = 1;

    case MEMEBER = 2;
    case GUEST = 3;

    static function rolesWithWritePerm(): array {
        return [self::CREATOR, self::MEMEBER];
    }

    static function rolesWithReadPerm(): array {
        return [...self::rolesWithWritePerm(), self::GUEST];
    }
}
