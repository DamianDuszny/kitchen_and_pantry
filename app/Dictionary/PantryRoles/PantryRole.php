<?php

namespace App\Dictionary\PantryRoles;
enum PantryRole: int {
    case OWNER = 1;
    case GUEST = 2;

    case TEST = 3;

    static function getRolesThatHaveWritePermission(): array {
        return [self::OWNER];
    }

    static function getRolesThatHaveReadPermission(): array {
        return [self::OWNER, self::GUEST];
    }
}
