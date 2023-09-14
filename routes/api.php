<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Token W/O
Route::post('login', [AuthController::class, 'login']);

//Token Required
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('blogs', BlogController::class)->only([
        'index', 'store'
    ]);
    Route::post('blog/{id}/toggle-like', [BlogController::class, 'toggle_like']);
});
