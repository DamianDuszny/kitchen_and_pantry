<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recipes_shared_to_users extends Model
{
    use HasFactory;

    protected $fillable = [
      'users_id',
       'recipes_id'
    ];
}
