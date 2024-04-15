<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
        'email_address',
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

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(products::class, '\App\Models\users_products_extra_data', 'user_id', 'product_id');
    }

    public function users_products_extra_data(): BelongsToMany
    {
        return $this->belongsToMany(products::class, 'users_products_extra_datas', 'user_id', 'product_id');
    }

    public function setFromRequest(\Illuminate\Http\Request $request): user {
        $this->first_name = $request->post('first_name') ?: $this->first_name;
        $this->email_address = $request->post('email_address') ?: $this->first_name;
        $this->last_name = $request->post('last_name') ?: $this->first_name;
        $this->password = Hash::make($request->post('password')) ?: $this->first_name;
        return $this;
    }
}
