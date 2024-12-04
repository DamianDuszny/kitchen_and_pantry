<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $description
 * @property string $name
 * @property int $users_id
 */
class pantry_roles extends Model
{
    protected $table = 'pantry';

    use HasFactory;

    protected $fillable = [
        'role_name',
    ];
}
