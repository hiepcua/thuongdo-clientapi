<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [OrderController::class, 'pagination']);
        Route::get('/status', [OrderController::class, 'reportStatus']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/get-debt/{ids}', [OrderController::class, 'getDebt']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);
        Route::get('/products/{code}', [OrderController::class, 'getProducts']);
        Route::get('/{id}', [OrderController::class, 'detail']);
        Route::get('/cancel/{order}', [OrderController::class, 'cancel']);
        Route::get('/split/{orderId}/{supplierId}', [OrderController::class, 'orderSplitBySupplier']);
    }
);
