<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class OrderPurchaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|in:0,1'
        ];
    }

    public function attributes()
    {
        return [
            'type' => 'Loại thanh toán',
        ];
    }
}
