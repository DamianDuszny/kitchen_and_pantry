<?php

use App\Http\Controllers\User\UserController;
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
    Route::get('/user/data', function (Request $request) {
        return $request->user();
    });
    Route::post('/user/update', [UserController::class, 'update']);
});

Route::put('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
