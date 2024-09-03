<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Awobaz\Compoships\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property int $users_id
 * @property int $products_id
 * @property int $unit_weight
 * @property int $net_weight
 * @property string $name
 * @property int $amount
 * @property int $price
 * @property users_products_descriptions $description
 * @property products $products_ean
 */
class users_products_extra_data extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    public $table = 'users_products_extra_data';

    protected $fillable = [
        'users_id',
        'products_id',
        'unit_weight',
        'net_weight',
        'amount',
        'price',
    ];

    public function description(): BelongsTo {
        return $this->belongsTo(users_products_descriptions::class, ['products_id', 'users_id'], ['products_id', 'users_id']);
    }

    public function products_ean(): BelongsTo {
        return $this->belongsTo(products::class, 'products_id');
    }
}




