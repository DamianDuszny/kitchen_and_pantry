<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recipes_products extends Model
{
    use HasFactory;
    protected $fillable = [
        'products_id',
        'recipes_id',
        'how_well_matches'
    ];
}
