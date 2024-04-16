<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property string $id
 * @property string $ean
 */
class products extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'ean',
    ];

//    public function users_products_extra_data(): BelongsToMany
//    {
//        return $this->belongsToMany(users_products_extra_data::class);
//    }
}
