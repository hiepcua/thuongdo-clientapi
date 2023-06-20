<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::patch('/order/{order}', [TransactionController::class, 'purchaseOrder']);
        Route::patch(
            '/order-supplier/{order}/{supplierId}',
            [TransactionController::class, 'purchaseOrderAndSupplier']
        );
    }
);