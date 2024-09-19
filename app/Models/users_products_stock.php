<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Awobaz\Compoships\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property int $users_id
 * @property int $products_id
 * @property int $unit_weight
 * @property int $net_weight
 * @property int $amount
 * @property int $price
 * @property string $expiration_date
 * @property users_products_descriptions $description
 * @property products $products_ean
 */
class users_products_stock extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    public $table = 'users_products_stock';

    protected $fillable = [
        'users_id',
        'products_id',
        'unit_weight',
        'net_weight',
        'amount',
        'price',
        'expiration_date',
    ];

    public function description(): HasOne {
        return $this->hasOne(users_products_descriptions::class, 'users_products_stock_id');
    }

    public function products_ean(): BelongsTo {
        return $this->belongsTo(products::class, 'products_id');
    }
}




