<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Awobaz\Compoships\Database\Eloquent\Model;

/**
 * @property int $users_id
 * @property int $products_id
 * @property string $name
 * @property string $img_url
 */
class users_products_descriptions extends Model
{
    use \Awobaz\Compoships\Compoships;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'users_id',
        'products_id',
        'name',
        'img_url',
    ];
}
