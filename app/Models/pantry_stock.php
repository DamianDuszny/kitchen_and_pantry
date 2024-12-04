<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Awobaz\Compoships\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property int $pantry_id
 * @property int $products_id
 * @property int $unit_weight
 * @property int $net_weight
 * @property int $amount
 * @property int $price
 * @property string $expiration_date
 * @property users_products_descriptions $description
 * @property products $products_ean
 */
class pantry_stock extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    public $table = 'pantry_stock';

    protected $fillable = [
        'pantry_id',
        'products_id',
        'unit_weight',
        'net_weight',
        'amount',
        'price',
        'expiration_date',
    ];

    public function description(): HasOne {//@todo users_products_stock_id? maybe pantry_stock_id
        return $this->hasOne(users_products_descriptions::class, 'users_products_stock_id');
    }

    public function products_ean(): BelongsTo {
        return $this->belongsTo(products::class, 'products_id');
    }

    public function pantry(): BelongsTo {
        return $this->belongsTo(pantry::class, 'pantry_id');
    }
}




