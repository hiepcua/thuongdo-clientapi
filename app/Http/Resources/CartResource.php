<?php

namespace App\Http\Resources;

use App\Constants\ConfigConstant;
use App\Helpers\MediaHelper;
use App\Services\AccountingService;
use App\Services\ConfigService;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CartResource extends JsonResource
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
            'cart_id' => $this->id,
            'supplier' => [
                'id' => optional($this->supplier)->id,
                'name' => optional($this->supplier)->name
            ],
            'total_amount_cny' => (float)$this->total_amount_cny,
            'total_amount' => (float)$this->total_amount_cny * (new ConfigService())->getExchangeCost(),
            'is_inspection' => (boolean)$this->is_inspection,
            'is_woodworking' => (boolean)$this->is_woodworking,
            'is_shock_proof' => (boolean)$this->is_shock_proof,
            'delivery_type' => $this->delivery_type,
            'note' => $this->note,
            'exchange_rate' => (float)(new ConfigService())->getValueByKey(ConfigConstant::CURRENCY_EXCHANGE_RATE),
            'products' => $this->getProductResource($this->products),
        ];
    }

    private function getProductResource($products): array
    {
        $data = [];
        $exchangeRate = (new ConfigService())->getExchangeCost();
        foreach ($products as $product) {
            $item = $product->only(
                ['id', 'name', 'link', 'unit_price_cny', 'quantity', 'amount_cny', 'classification', 'note', 'image']
            );
            $item['amount'] = $item['amount_cny'] * $exchangeRate;
            $item['order_fee'] = (new AccountingService())->getOrderFee($item['amount']);
            $item['image'] = MediaHelper::getDomain($item['image']);
            $data[] = $item;
        }
        return $data;
    }
}
