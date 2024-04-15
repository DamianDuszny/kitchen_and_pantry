<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $user_id
 * @property string $product_id
 * @property string $weight
 * @property string $name
 * @property string $amount
 * @property string $price
 */
class users_products_extra_data extends Model
{
    use HasFactory;

    public $table = 'users_products_extra_data';

    protected $fillable = [
        'users_id',
        'products_id',
        'weight',
        'name',
        'amount',
        'price',
    ];
}
