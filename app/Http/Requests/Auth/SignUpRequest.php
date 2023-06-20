<?php

namespace App\Http\Requests\Auth;

use App\Constants\PhoneNumberConstant;
use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone_number' => 'required|string|size:10|unique:customers|regex:(^[0-9+.]+$)|starts_with:'.StringHelper::convertArrayToString(
                    PhoneNumberConstant::PREFIX
                ),
            'warehouse_id' => 'required|string|size:36|exists:warehouses,id',
            'service' => 'required|in:0,1',
        ];
    }
}
