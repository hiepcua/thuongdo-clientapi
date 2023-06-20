<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/list', [CartController::class, 'index']);
        Route::post('/order', [CartController::class, 'orderUp']);
        Route::put('/{id}', [CartController::class, 'update']);
        Route::delete('/{id}', [CartController::class, 'destroy']);
        Route::delete('/supplier/{id}', [CartController::class, 'extDestroySupplier']);
        Route::group(
            ['prefix' => 'detail'],
            function () {
                Route::patch('/{cartDetail}', [CartController::class, 'updateCartDetail']);
            }
        );
        Route::group(
            ['prefix' => 'ext'],
            function () {
                Route::get('/', [CartController::class, 'extGetList']);
                Route::post('/', [CartController::class, 'extStore']);
                Route::patch('/detail/{cartDetail}', [CartController::class, 'updateCartDetail']);
                Route::delete('/detail/{id}', [CartController::class, 'destroy']);
                Route::delete('/supplier/{id}', [CartController::class, 'extDestroySupplier']);
            }
        );
    }
);
