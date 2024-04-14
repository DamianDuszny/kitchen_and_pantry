<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use \App\Http\Controllers\Products\ProductController;
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
});

Route::put('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
