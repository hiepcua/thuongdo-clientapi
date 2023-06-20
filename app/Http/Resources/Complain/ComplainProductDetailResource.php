<?php

namespace App\Http\Resources\Complain;

use App\Helpers\AccountingHelper;
use App\Models\ComplainDetail;
use App\Models\OrderDetailImage;
use App\Models\OrderDetailPackage;
use App\Services\OrderPackageService;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplainProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $detail = ComplainDetail::query()->where(['complain_id' => request()->id, 'order_detail_id' => $this->id])->first();
        return [
            'id' => $this->id,
            'bill_code' =>  (new OrderPackageService())->getBillCodesByIds([$detail->order_package_id]),
            'image' => $this->image,
            'name' => $this->name,
            'unit_price_cny' => $this->unit_price_cny,
            'quantity' => $quantity = optional(OrderDetailPackage::query()->where('order_package_id', $detail->order_package_id)->first())->quantity ?? 0,
            'amount_cny' => AccountingHelper::getCosts($this->unit_price_cny * $quantity),
            'complain_note' => $detail->note,
            'exchange_rate' => optional($this->order)->exchange_rate,
            'images' => optional(
                OrderDetailImage::query()->where(
                    ['complain_id' => $this->pivot->complain_id, 'order_detail_id' => $this->id]
                )
            )->pluck('image')
        ];
    }
}
