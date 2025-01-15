<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $description
 * @property string $name
 * @property pantry_stock $pantry_stock
 * @property pantry_users_access $users_privileges
 * @property user $users
 */
class pantry extends Model
{
    protected $table = 'pantry';

    use HasFactory;

    protected $fillable = [
        'description',
        'name',
    ];

    public function pantry_stock(): HasMany {
        return $this->hasMany(pantry_stock::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(user::class, 'pantry_users_access', relatedPivotKey: 'users_id');
    }

    public function users_privileges(): HasMany {
        return $this->hasMany(pantry_users_access::class);
    }
}
