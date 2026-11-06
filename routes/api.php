<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Admin\BatchController as AdminBatchController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\VariantController as AdminVariantController;


Route::prefix('v1')->group(function () {
    // Rutas públicas
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Rutas protegidas (requieren JWT válido)
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store']);
        
        // Rutas de perfil y notificaciones
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::patch('/profile/password', [ProfileController::class, 'changePassword']);
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
        Route::patch('/profile/notification-preferences', [ProfileController::class, 'updateNotificationPreferences']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

        // Rutas exclusivas para Administradores (requieren auth:api + admin)
        Route::middleware('admin')->group(function () {
            // Pagos (Admin)
            Route::get('/payments', [PaymentController::class, 'index']);
            Route::patch('/payments/{payment}/verify', [PaymentController::class, 'verify']);

            // Envíos (Admin)
            Route::get('/shipments', [ShipmentController::class, 'index']);
            Route::post('/orders/{order}/shipment', [ShipmentController::class, 'store']);
            Route::patch('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);    
        });

    });
    Route::prefix('admin')->group(function () {
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::post('/categories', [AdminCategoryController::class, 'store']);

    Route::get('/products', [AdminProductController::class, 'index']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::get('/products/{product}', [AdminProductController::class, 'show']);
    Route::patch('/products/{product}', [AdminProductController::class, 'update']);

    Route::post('/products/{product}/variants', [AdminVariantController::class, 'store']);
    Route::patch('/variants/{variant}', [AdminVariantController::class, 'update']);

    Route::get('/batches', [AdminBatchController::class, 'index']);
    Route::post('/batches', [AdminBatchController::class, 'store']);
    Route::patch('/batches/{batch}', [AdminBatchController::class, 'update']);
});
});