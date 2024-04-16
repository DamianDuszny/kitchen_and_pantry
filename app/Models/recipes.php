<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
