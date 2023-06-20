<?php

namespace App\Http\Resources\Order\Package;

use App\Models\Order;
use App\Models\Supplier;
use App\Services\ConfigService;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $record = $this->resource->first();
        return [
            'id' => $record->supplier_id,
            'name' => optional(Supplier::query()->find($record->supplier_id))->name,
            'exchange_rate' => (float)(optional(
                    Order::query()->find($record->order_id)
                )->exchange_rate ?? (new ConfigService())->getExchangeCost()),
            'products' => $this->resource->transform(
                fn($item) => $this->getProducts($item)
            )
        ];
    }

    private function getProducts($item): array
    {
        return $item->only(
                ['id', 'name', 'image', 'quantity', 'unit_price_cny', 'amount_cny', 'note']
            ) + ['category' => optional($item->category)->name];
    }
}
