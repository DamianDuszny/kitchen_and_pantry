<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $users_id
 * @property int $id
 * @property string $note
 */
class shopping_list extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;
    protected  $primaryKey = 'id';

    protected $fillable = [
        'users_id',
        'note',
    ];

    public function shoppingListProducts(): HasMany {
        return $this->hasMany(shopping_list_products::class, 'shopping_lists_id');
    }
}
