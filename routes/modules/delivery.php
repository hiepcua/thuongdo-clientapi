<?php

use App\Http\Controllers\DeliveryController;
use Illuminate\Support\Facades\Route;

Route::group(
    ['middleware' => 'auth:sanctum'],
    function () {
        Route::get('/', [DeliveryController::class, 'pagination']);
        Route::get('/detail/{id}', [DeliveryController::class, 'detail']);
        Route::delete('/{id}', [DeliveryController::class, 'destroy']);
        Route::get('/{orderId}', [DeliveryController::class, 'getListByOrder']);
        Route::post('/', [DeliveryController::class, 'storeByOrder']);
    }
);
