<?php

namespace App\Http\Resources\Organization;

use App\Models\Bank;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationBankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource =$this->resource->first();
        $bank = Bank::query()->find($resource->bank_id);
        return [
            'id' => $bank->id,
            'code' => $bank->bank_id,
            'bank_name' => $bank->name,
            'full_name' => $resource->name,
            'account_number' => $resource->account_number
        ];
    }
}
