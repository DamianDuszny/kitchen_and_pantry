<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property int $users_id
 * @property int $products_id
 * @property int $unit_weight
 * @property int $net_weight
 * @property string $name
 * @property int $amount
 * @property int $price
 */
class users_products_extra_data extends Model
{
    use HasFactory;

    public $table = 'users_products_extra_data';

    protected $fillable = [
        'users_id',
        'products_id',
        'unit_weight',
        'net_weight',
        'name',
        'amount',
        'price',
    ];
}
