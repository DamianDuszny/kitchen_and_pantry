<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $products_id
 * @property int $recipes_id
 * @property int $how_well_matches
 * @property int $amount
 */
class recipes_products extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;

    use HasFactory;
    protected $fillable = [
        'products_id',
        'recipes_id',
        'how_well_matches',
        'amount'
    ];
}
