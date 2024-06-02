<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $shopping_lists_id
 * @property int $products_id
 * @property int $recipes_id
 * @property int $amount
 * @property int $weight
 * @property int $substitute_for
 * @property int $satisfied_amount
 * @property string $note
 */
class shopping_list_products extends Model
{
    use HasFactory;

    protected $fillable = [
        'shopping_lists_id',
        'products_id',
        'recipes_id',
        'amount',
        'weight',
        'substitute_for',
        'satisfied_amount',
        'note'
    ];
}
