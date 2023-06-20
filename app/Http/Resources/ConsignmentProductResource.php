<?php

namespace App\Http\Resources;

use App\Helpers\AccountingHelper;
use App\Models\OrderPackage;
use App\Services\ConfigService;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsignmentProductResource extends JsonResource
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
            'exchange_rate' => $exchangeRate = (new ConfigService())->getExchangeCost(),
            'products' => $this->getProducts($this->resource, $exchangeRate)
        ];
    }

    private function getProducts($item, float $exchangeRate): array
    {
        $data = $item->only(
                ['id', 'name', 'image', 'quantity', 'order_cost']
            ) + ['category' => optional($item->category)->name];
        $data['amount_cny'] = AccountingHelper::getCosts($data['order_cost'] / $exchangeRate);
        $data['unit_price_cny'] = AccountingHelper::getCosts($data['amount_cny'] / $data['quantity']);
        $data['note'] = optional($item->orderPackage)->note;
        unset($data['order_cost']);
        return $data;
    }
}
