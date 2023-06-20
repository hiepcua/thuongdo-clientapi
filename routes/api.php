<?php

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/handle', function() {
    $orderIds = ['7574c5d6-0077-4067-b9cb-846444ce334b', '9516549d-1a5c-43c2-a232-9739c0b520e1'];
    $orders = Order::query()->findMany($orderIds);
    dd($orders->sum('total_amount'));
});
