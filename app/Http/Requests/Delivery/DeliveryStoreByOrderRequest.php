<?php

namespace App\Http\Requests\Delivery;

use App\Constants\TransporterConstant;
use App\Models\Transporter;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryStoreByOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $ids = Transporter::query()->whereIn('name', TransporterConstant::WITHOUT_TYPE)->pluck('id')->all();
        return [
            'packages' => 'required|array',
            'packages.*' => 'uuid|exists:order_package,id',
            'transporter_id' => 'required|uuid',
            'transporter_detail_id' => 'uuid',
            'type' => ['in:normal,fast', 'required_if:transporter_id,'.implode(',', $ids)],
            'payment' => 'required|in:e-wallet,cod',
            'customer_delivery_id' => 'required|uuid',
            'note' => 'string|max:500',
            'date' => 'required|date:Y-m-d|after_or_equal:'.date('Y-m-d'),
        ];
    }
}
