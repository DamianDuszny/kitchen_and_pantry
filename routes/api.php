<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RecipesController;
use App\Http\Controllers\Api\ShoppingListController as ShoppingListController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('user')->group(function() {
        Route::get('/data', function (Request $request) {
            return $request->user();
        });
        Route::post('/update', [UserController::class, 'update']);
    });
    Route::resource('/products', ProductController::class);
    Route::prefix('recipes')->group(function() {
        Route::get('/', [RecipesController::class, 'index']);
        Route::post('/', [RecipesController::class, 'store']);
        Route::get('/{recipe_id}', [RecipesController::class, 'show']);
        Route::delete('/{recipe_id}', [RecipesController::class, 'deleteRecipe']);
        Route::delete('/{recipe_id}/substitutes/{substituteProductId}', [RecipesController::class, 'deleteSubstituteProduct']);
        Route::post('/add-substitutes', [RecipesController::class, 'addSubstitutes']);
    });

    Route::post('/shopping-list', [ShoppingListController::class, 'createShoppingListFromRecipes']);
});

Route::put('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
