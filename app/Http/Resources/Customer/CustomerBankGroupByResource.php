<?php

namespace App\Http\Resources\Customer;

use App\Models\Bank;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CustomerBankGroupByResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $bank = Bank::query()->find($this->resource->first()->bank_id);
        $data = $bank->only('name', 'bank_id', 'short_name');
        $data['accounts'] = $this->resource instanceof Collection ? $this->resource->transform(
            function ($item) {
                return $item->only('id', 'account_holder', 'account_number', 'branch');
            }
        ) : [$this->resource->only('id', 'account_holder', 'account_number', 'branch')];
        return $data;
    }
}
