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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

Route::prefix('backend')->middleware('auth')->namespace('Backend')->group(function () {
    // Route::get('dashboard', 'DashboardController')->name('dashboard');

    Route::middleware('can:Admin')->namespace('Admin')->as('admin.')->group(function () {
        Route::apiResource('category', 'CategoryController');
        Route::get('orders', 'OrderController')->name('orders');
        Route::get('products', 'ProductController')->name('products');
    });

    Route::middleware('can:Seller')->namespace('Seller')->as('seller.')->group(function () {
        Route::apiResource('product', 'ProductController');
    });

});

Route::namespace ('Frontend')->group(function () {
    Route::middleware(['auth', 'can:Customer'])->namespace('Customer')->as('customer.')->group(function () {
        Route::apiResource('order', 'OrderController');
        Route::delete('cart', 'CartController@clear')->name('cart.clear');
        Route::apiResource('cart', 'CartController')->except('update', 'show');
    });
});
