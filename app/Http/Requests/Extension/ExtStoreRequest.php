<?php

namespace App\Http\Requests\Extension;

use Illuminate\Foundation\Http\FormRequest;

class ExtStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'supplier' => 'required|string|max:255',
            'name' => 'required|string:max:255',
            'image' => 'required|string:max:255',
            'link' => 'url|max:255',
            'classification' => 'max:255',
            'unit_price_cny' => 'required|min:0|numeric',
            'quantity' => 'required|min:0|numeric'
        ];
    }

    public function attributes(): array
    {
        return [
            'classification' => 'Phân loại hàng',
            'link' => 'Link'
        ];
    }
}
