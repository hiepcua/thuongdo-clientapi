<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Services\ConfigService;
use App\Services\CustomerService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = $this->resource->only(
            [
                'id',
                'name',
                'email',
                'avatar',
                'code',
                'phone_number',
                'bod',
                'gender',
                'address',
                'district_id',
                'province_id',
                'delivery_type',
                'feelings',
                'level',
                'warehouse_id',
                'service'
            ]
        );
        // TODO: Tính tổng đơn hàng đã đặt và mốc lên level
        $data['order_amount'] = (float)Order::query()->where(
            'deposit_cost',
            '>',
            0
        )->sum('order_cost');
        $result = (new ConfigService())->getResultFromValueByLevel((new CustomerService())->getLevelByCurrentUser());
        $data['level_milestone'] = $result->max;
        $data['e_wallet'] =  (new CustomerService())->getBalanceAmount();
        unset($result->max);
        $data['offer'] = $result;
        return $data;
    }
}
