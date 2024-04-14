<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $users_id
 * @property string $products_id
 * @property string $weight
 * @property string $name
 * @property string $amount
 * @property string $price
 */
class users_products_extra_data extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'products_id',
        'weight',
        'name',
        'amount',
        'price',
    ];
}
