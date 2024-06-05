<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $shopping_lists_id
 * @property int $products_id
 * @property int $recipes_id
 * @property int $amount
 * @property int $weight
 * @property bool $accepted
 * @property int $substitute_for
 * @property int $satisfied_amount
 * @property string $note
 */
class shopping_list_products extends Model
{
    use HasFactory, Timestamp;

    protected $primaryKey = "id";

    protected $fillable = [
        'shopping_lists_id',
        'products_id',
        'recipes_id',
        'amount',
        'weight',
        'accepted',
        'substitute_for',
        'satisfied_amount',
        'note'
    ];

    public function shoppingList() :BelongsTo {
        return $this->belongsTo('shopping_list', 'shopping_lists_id');
    }
}
