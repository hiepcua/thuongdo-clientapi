<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartDetailUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'link' => 'url|max:255',
            'quantity' => 'numeric|min:1'
        ];
    }
}
