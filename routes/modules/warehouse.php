<?php

use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::get('/{country}', [WarehouseController::class, 'getListByCountry']);
});
