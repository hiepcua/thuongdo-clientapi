<?php

namespace App\Http\Resources;

use App\Constants\ConsignmentConstant;
use App\Constants\TimeConstant;
use App\Helpers\StatusHelper;
use App\Helpers\TimeHelper;
use App\Http\Resources\Package\ConsignmentPackageResource;
use App\Models\Consignment;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsignmentResource extends JsonResource
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
            'code' => $this->code,
            'ordered_at' => TimeHelper::format($this->created_at, TimeConstant::DATE_VI),
            'warehouse_cn' => optional($this->warehouseCn)->custom_name,
            'warehouse_vi' => optional($this->warehouseVi)->custom_name,
            'packages_number' => $this->packages_number ?? 0,
            'address_receiver' => optional($this->customerDelivery)->custom_name,
            'is_cancel' => $this->status !== ConsignmentConstant::KEY_STATUS_PENDING,
            'status' => StatusHelper::getInfo($this->status, ConsignmentConstant::class),
            'statuses' => StatusHelper::getStatuses(
                $this->id,
                Consignment::class,
                ConsignmentConstant::class,
                ConsignmentConstant::STATUES_SHOW_DETAILS
            ),
            'packages' => new ListResource($this->packages, ConsignmentPackageResource::class),
        ];
    }
}
