<?php

use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [SupplierController::class, 'getList']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);

    }
);
