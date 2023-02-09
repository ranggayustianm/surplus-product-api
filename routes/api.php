<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
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

Route::resource('products', ProductController::class);
Route::get('/products/{id}/images', [ProductController::class, 'showImages']);
Route::get('/products/{id}/categories', [ProductController::class, 'showCategories']);
Route::patch('/products/{id}/{enableValue}', [ProductController::class, 'setEnable']);
Route::put('/products/{id}/images', [ProductController::class, 'setImages']);
Route::put('/products/{id}/categories', [ProductController::class, 'setCategories']);