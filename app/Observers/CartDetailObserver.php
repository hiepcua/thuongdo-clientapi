<?php

namespace App\Observers;

use App\Models\CartDetail;
use App\Services\CartDetailService;

class CartDetailObserver
{
    /**
     * Handle the Customer "updated" event.
     *
     * @param  CartDetail  $detail
     * @return void
     */
    public function updated(CartDetail $detail)
    {
        // Cập nhật lại tổng giá trị đơn hàng
        if ($detail->amount_cny !== $detail->quantity * $detail->unit_price_cny) {
            (new CartDetailService())->updateAmountByQuantity($detail);
        }
    }
}
