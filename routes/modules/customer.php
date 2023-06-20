<?php

use App\Http\Controllers\CustomerBankController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDeliveryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::put('/', [CustomerController::class, 'profile']);
        Route::get('/me', [CustomerController::class, 'me']);
        Route::patch('/change-password', [CustomerController::class, 'changePassword']);
        Route::group(
            ['prefix' => 'bank'],
            function () {
                Route::get('/', [CustomerBankController::class, 'pagination']);
                Route::get('/list', [CustomerBankController::class, 'index']);
                Route::post('/', [CustomerBankController::class, 'store']);
                Route::put('/{id}', [CustomerBankController::class, 'update']);
                Route::delete('/{id}', [CustomerBankController::class, 'destroy']);
            }
        );
        Route::group(
            ['prefix' => 'delivery'],
            function () {
                Route::get('/', [CustomerDeliveryController::class, 'index']);
                Route::post('/', [CustomerDeliveryController::class, 'store']);
                Route::put('/{id}', [CustomerDeliveryController::class, 'update']);
                Route::delete('/{id}', [CustomerDeliveryController::class, 'destroy']);
            }
        );
    }
);
