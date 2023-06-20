<?php


namespace App\Services;


use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Http\JsonResponse;

class ExtensionService implements Service
{
    public function destroy(string $id): JsonResponse
    {
        $cart = Cart::query()->where('supplier_id', $id);
        $ids = (clone $cart)->pluck('id')->all();
        CartDetail::query()->whereIn('cart_id', $ids)->delete();
        $cart->delete();
        return resSuccess();
    }
}