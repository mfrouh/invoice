<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';

Route::prefix('backend')->middleware(['auth', 'can:Admin'])->namespace('Backend')->group(function () {
    Route::get('dashboard', 'DashboardController')->name('dashboard');
    Route::get('orders', 'OrderController')->name('orders');
    Route::apiResource('category', 'CategoryController');
    Route::apiResource('product', 'ProductController');

});

Route::namespace ('Frontend')->middleware(['auth', 'can:Customer'])->group(function () {
    Route::get('dashboard', 'DashboardController')->name('dashboard');
    Route::apiResource('order', 'OrderController');
    Route::delete('cart', 'CartController@clear')->name('cart.clear');
    Route::apiResource('cart', 'CartController')->except('update', 'show');
});
