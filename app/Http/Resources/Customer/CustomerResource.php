<?php

namespace App\Http\Resources\Customer;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $report = $this->report;
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'last_order_at' => $this->last_order_at,
            'label' => optional($this->label)->only('id', 'name'),
            'via' => $this->via,
            'level' => $this->level,
            'status' => $this->status,
            'orders_number' => (int)($report->orders_number ?? 0),
            'order_consignment_number' => (int)($report->consignment_number ?? 0),
            'order_packages_number' => (int)($report->packages_number ?? 0),
            'deposited_amount' => (float)($report->deposited_amount ?? 0),
            'balance_amount' => (float)($report->balance_amount ?? 0),
        ];
    }
}
