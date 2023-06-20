<?php

namespace App\Http\Resources\Complain;

use App\Constants\ComplainConstant;
use App\Constants\OrderConstant;
use App\Helpers\StatusHelper;
use App\Http\Resources\ListResource;
use App\Models\Complain;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ComplainDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'order' => [
                'id' => optional($this->order)->id,
                'code' => optional($this->order)->code,
                'status' => StatusHelper::getInfo(optional($this->order)->status, OrderConstant::class)
            ],
            'statuses' => $this->getStatuses($this->id),
            'complain_type' => optional($this->complainType)->name,
            'solution' => optional($this->solution)->name,
            'staff_order' => optional($this->staffOrder)->name,
            'staff_complain' => optional($this->staffComplain)->name,
            'products' => new ListResource($this->orderDetails, ComplainProductDetailResource::class),
            'images_bill' => optional($this->images)->where('is_bill', 1)->pluck('image'),
            'images_received' => optional($this->images)->where('is_bill', 0)->pluck('image'),
            'feedbacks' => new ListResource($this->feedbacks, FeedbackResource::class)
        ];
    }

    private function getStatuses(string $id): array
    {
        $statuses = array_keys(ComplainConstant::STATUSES);
        array_unshift($statuses, $statuses[0]);
        array_pop($statuses);
        $statuses = StatusHelper::getStatuses($id, Complain::class, ComplainConstant::class, $statuses);
        $statuses[0]['name'] = 'Tạo khiếu nại';
        return $statuses;
    }
}
