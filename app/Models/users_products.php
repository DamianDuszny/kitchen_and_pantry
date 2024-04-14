<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $users_id
 * @property string $products_id
 */
class users_products extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];
}
