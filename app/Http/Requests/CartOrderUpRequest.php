<?php

namespace App\Http\Requests;

use App\Constants\OrderConstant;
use Illuminate\Foundation\Http\FormRequest;

class CartOrderUpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'carts' => 'required|array',
            'carts.*.id' => 'required|exists:carts,id',
            'carts.*.note' => 'nullable|max:500',
            'carts.*.delivery_type' => 'in:' . implode(',', array_keys(OrderConstant::DELIVERIES_TEXT)),
            'carts.*.is_woodworking' => 'nullable|bool',
            'carts.*.is_inspection' => 'nullable|bool',
            'carts.*.is_shock_proof' => 'nullable|bool',
            'carts.*.products' => 'required|array',
            'carts.*.products.*.id' => 'required|exists:cart_details,id',
            'carts.*.products.*.modifies.quantity' => 'required|numeric|min:1',
            'carts.*.products.*.modifies.note' => 'nullable|max:500',
            'customer_delivery_id' => 'required|exists:customer_deliveries,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'note' => 'max:500'
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'carts' => 'Giỏ hàng',
            'carts.*.id' => 'Giỏ hàng',
            'carts.*.note' => 'Ghi chú',
            'carts.*.delivery_type' => 'Vận chuyển',
            'carts.*.is_woodworking' => 'Đóng kiện',
            'carts.*.is_inspection' => 'Kiểm hàng',
            'carts.*.is_shock_proof' => 'Chống shock',
            'carts.*.products' => 'Sản phẩm',
            'carts.*.products.*.id' => 'Sản phẩm',
            'carts.*.products.*.modifies.quantity' => 'Số lượng sản phẩm',
            'carts.*.products.*.modifies.note' => 'Ghi chú sản phẩm',
            'customer_delivery_id' => 'Người nhận',
            'warehouse_id' => 'Kho tại Việt Nam',
            'note' => 'note'
        ];
    }
}
