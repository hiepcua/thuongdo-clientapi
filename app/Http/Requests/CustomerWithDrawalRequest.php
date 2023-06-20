<?php

namespace App\Http\Requests;

use App\Helpers\ConvertHelper;
use App\Services\CustomerService;
use Illuminate\Foundation\Http\FormRequest;

class CustomerWithDrawalRequest extends FormRequest
{
    private float $_balance;
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->_balance = (new CustomerService())->getBalanceAmount();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|between:1,'.$this->_balance,
            'customer_bank_id' => 'required|uuid|exists:customer_banks,id'
        ];
    }

    public function messages()
    {
        return [
            'amount.between' => 'Số dư của bạn không đủ để thực hiện giao dịch'
       ];
    }

    public function attributes()
    {
        return [
            'amount' => 'Tiền rút',
            'customer_bank_id' => 'Ngân hàng'
        ];
    }
}
