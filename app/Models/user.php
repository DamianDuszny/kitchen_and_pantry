<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $email_address
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $password
 * @property pantry[] $pantries
 */
class user extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];
    public function pantries(): BelongsToMany {
        return $this->belongsToMany(pantry::class, 'pantry_users_access', 'users_id');
    }

    public function products_stock(): hasMany {
        return $this->hasMany(pantry_stock::class);
    }

    public function setFromRequest(\Illuminate\Http\Request $request): self {
        $this->first_name = $request->post('first_name') ?: $this->first_name;
        $this->email_address = $request->post('email_address') ?: $this->first_name;
        $this->last_name = $request->post('last_name') ?: $this->first_name;
        $this->password = Hash::make($request->post('password')) ?: $this->first_name;
        return $this;
    }

    public function recipes(): HasMany
    {
        return $this->HasMany(recipes::class);
    }
}
