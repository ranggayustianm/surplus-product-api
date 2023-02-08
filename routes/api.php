<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('categories', CategoryController::class);
Route::get('/categories/{id}/products', [CategoryController::class, 'showProducts']);
Route::patch('/categories/{id}/{enableValue}', [CategoryController::class, 'setEnable']);

Route::resource('images', ImageController::class);
Route::get('/images/{id}/products', [ImageController::class, 'showProducts']);
Route::patch('/images/{id}/{enableValue}', [ImageController::class, 'setEnable']);