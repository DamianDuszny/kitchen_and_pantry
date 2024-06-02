<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $users_id
 * @property int $id
 * @property string $notes
 */
class shopping_list extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'notes',
    ];
}
