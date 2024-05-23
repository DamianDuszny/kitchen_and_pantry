<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shopping_lists_products extends Model
{
    use HasFactory;

    protected $fillable = [
        'products_id',
        'amount',
        'net_weight',
        'bought',
        'note'
    ];
}
