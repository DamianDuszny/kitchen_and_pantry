<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $recipes_id
 * @property int $substitute_for
 * @property int $products_id
 * @property int $amount
 * @property int $weight
 * @property string $comment
 * @property int $how_well_fits
 */
class recipes_substitute_products extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;

    use HasFactory;
    protected $fillable = [
        'recipes_id',
        'substitute_for',
        'products_id',
        'amount',
        'weight',
        'comment',
        'how_well_fits'
    ];
}
