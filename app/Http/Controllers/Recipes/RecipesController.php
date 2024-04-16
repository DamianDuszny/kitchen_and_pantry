<?php

namespace App\Http\Controllers\Recipes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecipesController extends Controller
{
    public function index(Request $request) {
        echo 'index';
    }

    public function store(Request $request) {
        echo 'store';
    }
}
