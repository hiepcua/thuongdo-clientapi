<?php


namespace App\Services;


use App\Models\CartDetail;


class CartDetailService extends BaseService
{
    /**
     * @param  CartDetail  $detail
     */
    public function updateAmountByQuantity(CartDetail $detail): void
    {
        $detail->amount_cny = $detail->quantity * $detail->unit_price_cny;
        $detail->save();
    }

    /**
     * @param  string  $id
     * @return float
     */
    public function getTotalAmountCnyByCartId(string $id): float
    {
        return CartDetail::query()->where('cart_id', $id)->sum('amount_cny');
    }
}