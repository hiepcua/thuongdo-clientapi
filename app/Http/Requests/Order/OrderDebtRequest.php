<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderDebtRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'orders' => 'required|array',
            'orders.*' => 'required|exists:orders,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'orders' => 'Đơn hàng',
            'orders.*' => 'Đơn hàng',
        ];
    }
}
