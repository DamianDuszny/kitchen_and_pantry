<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $ean
 * @property string $type
 */
class products extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'ean',
        'type',
    ];
}
