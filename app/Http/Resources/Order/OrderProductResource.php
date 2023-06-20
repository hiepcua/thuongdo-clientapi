<?php

namespace App\Http\Resources\Order;

use App\Services\OrderPackageService;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'bill_code' => (new OrderPackageService())->getBillCodesByIds(explode(', ', request()->ids)),
            'image' => $this->image,
            'name' => $this->name,
            'unit_price_cny' => $this->unit_price_cny,
            'quantity' => $this->quantity,
            'amount_cny' => $this->amount_cny,
            'exchange_rate' => optional($this->order)->exchange_rate,
            'category' => optional($this->category)->name,
        ];
    }
}
