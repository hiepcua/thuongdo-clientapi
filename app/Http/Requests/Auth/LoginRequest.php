<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'username' => 'Email hoặc Số Điện Thoại',
            'passowrd' => 'Mật khẩu'
        ];
    }
}
