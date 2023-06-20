<?php

namespace App\Http\Resources\Customer;

use App\Constants\CustomerConstant;
use App\Constants\TimeConstant;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerWithdrawalResource extends JsonResource
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
            'time' => $this->created_at->format(TimeConstant::DATE_VI),
            'info' => $this->info,
            'amount' => $this->amount,
            'status' => [
                'value' => $this->status,
                'name' => CustomerConstant::WITHDRAWAL_STATUSES[$this->status],
                'color' => CustomerConstant::WITHDRAWAL_COLOR[$this->status]
            ]
        ];
    }
}
