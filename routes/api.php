<?php

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

Route:: as ('api.')->group(function () {
    Route::post('/login', 'Api\Auth\AuthController@login')->name('login');
    Route::post('/register', 'Api\Auth\AuthController@register')->name('register');
    Route::post('/me', 'Api\Auth\AuthController@me')->middleware('auth:sanctum')->name('userInformation');
    Route::post('/logout', 'Api\Auth\AuthController@logout')->middleware('auth:sanctum')->name('logout');

    Route::prefix('backend')->middleware(['auth:sanctum', 'can:Admin'])->namespace('Backend')->group(function () {
        Route::get('dashboard', 'DashboardController')->name('dashboard');
        Route::get('orders', 'OrderController')->name('orders');
        Route::post('category/change-status', 'CategoryController@changeStatus')->name('category.change-status');
        Route::post('product/change-status', 'ProductController@changeStatus')->name('product.change-status');
        Route::apiResource('category', 'CategoryController');
        Route::apiResource('product', 'ProductController');
        Route::apiResource('attribute', 'AttributeController');
        Route::apiResource('value', 'ValueController');
        Route::apiResource('variant', 'VariantController');
        Route::apiResource('coupon', 'CouponController');
        Route::apiResource('offer', 'OfferController');
        Route::apiResource('review', 'ReviewController')->only('index');
        Route::apiResource('setting', 'SettingController')->only(['store']);
    });

    Route::namespace ('Frontend')->middleware(['auth:sanctum', 'can:Customer'])->group(function () {
        Route::get('dashboard', 'DashboardController')->name('dashboard');
        Route::apiResource('order', 'OrderController');
        Route::delete('cart', 'CartController@clear')->name('cart.clear');
        Route::apiResource('cart', 'CartController')->except('update', 'show');
    });

    Route::namespace ('Setting')->middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('profile-setting', 'ProfileSettingController')->only(['store']);
        Route::apiResource('change-password', 'ChangePasswordController')->only(['store']);
    });

});
