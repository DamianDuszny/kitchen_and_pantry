<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $user_id
 * @property string $name
 * @property int $kcal
 * @property int $portion_for_how_many
 * @property string $description
 * @property string $instruction
 * @property int $preparation_time
 * @property int $complexity
 * @property int $type
 * @property int $id
 */
class recipes extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'kcal',
        'portion_for_how_many',
        'preparation_time',
        'complexity',
        'description',
        'instruction',
        'type'
    ];

    public function type():BelongsTo {
        return $this->belongsTo(recipes_types::class);
    }

    public function recipe_products(): BelongsToMany {
        return $this->belongsToMany(products::class, 'recipes_products')->withPivot('*');
    }

    public function recipe_substitute_products(): BelongsToMany {
        return $this->BelongsToMany(products::class, 'recipes_substitute_products')->withPivot('*');
    }

    public static function userRecipe(Builder $builder, int $userId) : Builder {
        return $builder->where('user_id', $userId);
    }
}
