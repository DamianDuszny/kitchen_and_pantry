<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $pantry_id
 * @property int $users_id
 * @property int $role_id
 */
class pantry_users_access extends Model
{
    use HasFactory;

    protected $table = 'pantry_users_access';
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'pantry_id',
        'users_id',
        'role_id'
    ];

    public function pantry(): BelongsTo {
        return $this->belongsTo('pantry');
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany('user');
    }

    public function pantry_roles(): BelongsToMany {
        return $this->belongsToMany('pantry_roles');
    }
}
