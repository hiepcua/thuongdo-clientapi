<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerBankResource extends JsonResource
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
            'name' => $this->account_holder,
            'bank' => [
                'id' => optional($this->bank)->id,
                'code' => optional($this->bank)->bank_id,
                'name' => optional($this->bank)->name,
                'short_name' => optional($this->bank)->short_name,
            ],
            'account_number' => $this->account_number,
            'branch' => $this->branch
        ];
    }
}
