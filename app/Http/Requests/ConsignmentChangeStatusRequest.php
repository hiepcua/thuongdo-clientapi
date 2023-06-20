<?php

namespace App\Http\Requests;

use App\Constants\ConsignmentConstant;
use Illuminate\Foundation\Http\FormRequest;

class ConsignmentChangeStatusRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:'.implode(',', array_keys(ConsignmentConstant::STATUSES))
        ];
    }
}
