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

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\AdminOrderTrackingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- 1. ROUTE GUEST / PUBLIC (Tanpa Auth / Belum Login) ---
Route::prefix('auth')->middleware('guest')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});

// Catalog Public
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Webhook / Callback Midtrans
Route::post('/payments/callback', [PaymentController::class, 'callback']);


// --- 2. ROUTE PROTECTED (Wajib Login / Bearer Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    // User Profile & Logout
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user', [UserController::class, 'update']);
    Route::post('/auth/logout', [UserController::class, 'logout']);

    // Alamat Pengiriman (CRUD)
    Route::apiResource('addresses', AddressController::class);

    // Keranjang Belanja
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartItemController::class, 'store']);
    Route::put('/cart/items/{id}', [CartItemController::class, 'update']);
    Route::delete('/cart/items/{id}', [CartItemController::class, 'destroy']);

    // Pesanan / Checkout
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Tracking Pesanan
    Route::get('/orders/{id}/tracking', [OrderTrackingController::class, 'index']);

    // Pembayaran
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{order_id}', [PaymentController::class, 'show']);
});


// --- 3. ROUTE ADMIN (Khusus Akun Admin) ---
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', AdminCategoryController::class);
    Route::apiResource('products', AdminProductController::class);
    Route::apiResource('couriers', CourierController::class);
    Route::post('/orders/{id}/tracking', [AdminOrderTrackingController::class, 'store']);
});