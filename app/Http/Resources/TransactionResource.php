<?php

namespace App\Http\Resources;

use App\Constants\TransactionConstant;
use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'time' => TimeHelper::format($this->created_at),
            'code' => $this->code,
            'content' => $this->content,
            'amount' => $this->amount * ($this->is_positive ? 1 : -1),
            'balance' => $this->balance,
            'status' => [
                'value' => $this->status,
                'name' => TransactionConstant::STATUSES[$this->status],
                'color' => TransactionConstant::STATUSES_COLOR[$this->status],
            ]
        ];
    }
}
