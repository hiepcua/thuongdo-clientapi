<?php

namespace App\Http\Resources\Package;

use App\Helpers\StatusHelper;
use App\Helpers\TimeHelper;
use App\Models\OrderPackage;
use App\Services\OrderPackageService;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsignmentPackageResource extends JsonResource
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
            'bill_code' => $this->bill_code,
            'created_at' => TimeHelper::format($this->created_at),
            'status' => $status = (new OrderPackageService())->getStatus($this->status) + StatusHelper::getTime(
                    $this->id,
                    OrderPackage::class,
                    $this->status
                ),
            'type_of_goods' => 'Ký gửi',
            'weight_origin' => round($this->weight,2) ?? 0,
            'weight_round' => round($this->weight) ?? 0,
            'transporter' => optional($this->transporterRelation)->name ?? $this->transporter,
            'amount' => $this->amount,
            'category' => optional($this->categoryRelation)->name ?? $this->category,
        ];
    }
}
