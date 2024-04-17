<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class recipes extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'kcal',
        'portion_for_how_many',
        'complexity',
    ];

    public function type():BelongsTo {
        return $this->belongsTo(recipes_types::class);
    }
}
