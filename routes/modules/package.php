<?php

use App\Http\Controllers\OrderPackageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [OrderPackageController::class, 'pagination']);
        Route::get('/products/{id}', [OrderPackageController::class, 'getProductsById']);
        Route::get('/products-by-multiple-packages/{ids}', [OrderPackageController::class, 'getProductsByMultipleIds']);
        Route::get('/order/{orderId}', [OrderPackageController::class, 'getPackageByOrderId']);
        Route::get('/delivery/{deliveryId}', [OrderPackageController::class, 'getPackageByDeliveryId']);
        Route::get('/statuses', [OrderPackageController::class, 'getStatuses']);
        Route::get('/{id}', [OrderPackageController::class, 'detail']);
        Route::patch('/{id}', [OrderPackageController::class, 'addNote']);
        Route::delete('/{id}', [OrderPackageController::class, 'destroy']);
    }
);
