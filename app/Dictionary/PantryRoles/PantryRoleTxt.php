<?php

namespace App\Dictionary\PantryRoles;

class PantryRoleTxt
{
    static function getRoleName(PantryRole $role) {
        return match($role) {
            PantryRole::CREATOR => 'Założyciel',
            PantryRole::MEMEBER => 'Domownik',
            PantryRole::GUEST => 'Gość',
        };
    }
}
