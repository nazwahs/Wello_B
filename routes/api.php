<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\AdminOrderTrackingController;

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::post('/payments/callback', [PaymentController::class, 'callback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put('/user', [UserController::class, 'update']);


    Route::apiResource('addresses', AddressController::class);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartItemController::class, 'store']);
    Route::put('/cart/items/{id}', [CartItemController::class, 'update']);
    Route::delete('/cart/items/{id}', [CartItemController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/{id}/tracking', [OrderTrackingController::class, 'index']);

    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{order_id}', [PaymentController::class, 'show']);
}); 

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{slug}', [CategoryController::class, 'show']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{slug}', [ProductController::class, 'show']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        Route::apiResource('couriers', CourierController::class);

        Route::post(
            '/orders/{id}/tracking',
            [AdminOrderTrackingController::class, 'store']
        );
    });