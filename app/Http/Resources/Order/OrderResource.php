<?php

namespace App\Http\Resources\Order;

use App\Constants\ColorConstant;
use App\Constants\OrderConstant;
use App\Constants\TimeConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\StatusHelper;
use App\Helpers\TimeHelper;
use App\Http\Resources\Customer\CustomerDeliveryResource;
use App\Http\Resources\ReportStatusResource;
use App\Http\Resources\Warehouse\WarehouseResource;
use App\Models\Order;
use App\Models\OrderSupplier;
use App\Models\Supplier;
use App\Services\ComplainService;
use App\Services\DeliveryService;
use App\Services\OrderPackageService;
use App\Services\OrderService;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use JsonSerializable;

class OrderResource extends JsonResource
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
            'order_id' => $this->id,
            'suppliers' => $this->getSuppliers(
                $this->products->groupBy('supplier_id'),
                (float)$this->exchange_rate,
                (bool)$this->is_inspection
            ),
            'order_code' => $this->code,
            'total_amount' => AccountingHelper::getCosts((float)$this->total_amount),
            'order_cost' => AccountingHelper::getCosts((float)$this->order_cost),
            'order_fee' => (float)$this->order_fee,
            'delivery_cost' => (float)$this->delivery_cost,
            'delivery_type' => $this->delivery_type,
            'inspection_cost' => (float)$this->inspection_cost,
            'woodworking_cost' => (float)$this->woodworking_cost,
            'international_shipping_cost' => (float)$this->international_shipping_cost,
            'china_shipping_cost' => (float)$this->china_shipping_cost,
            'discount_cost' => (float)$this->discount_cost,
            'deposit_cost' => $deposit = (float)$this->deposit_cost,
            'is_purchase' => $deposit == 0 && $this->status === OrderConstant::KEY_STATUS_WAITING_DEPOSIT,
            'note' => $this->note,
            'status' => new ReportStatusResource($this->status, OrderConstant::class),
            'date_ordered' => TimeHelper::format($this->date_ordered, TimeConstant::DATE_VI),
            'packages_number' => $this->packages_number,
            'exchange_rate' => (float)$this->exchange_rate,
            'delivery' => new CustomerDeliveryResource($this->delivery),
            'warehouse' => new WarehouseResource($this->warehouse),
            'services' => OrderConstant::DELIVERIES_TEXT[$this->delivery_type].((float)$this->inspection_cost > 0 ? ', Kiểm hàng' : ''),
            'reports' => [
                'packages' => [
                    'quantity' => $this->packages_number,
                    'color' => $this->getColorByObject($this->id, $this->packages_number, 'packages')
                ],
                'complains' => [
                    'quantity' => $this->complains_number,
                    'color' => $this->getColorByObject($this->id, $this->complains_number, 'complains')
                ],
                'deliveries' => [
                    'quantity' => $this->deliveries_number,
                    'color' => $this->getColorByObject($this->id, $this->deliveries_number, 'deliveries')
                ]
            ],
            'is_delete' => !(new OrderService())->updatingNotAllow($this->status),
            'can_make_complain' => (int)StatusHelper::getIndexKey($this->status) >= OrderConstant::STATUS_ORDERED
        ];
    }

    /**
     * @param  string  $orderId
     * @param  int  $quantity
     * @param  string  $object
     * @return string
     */
    public function getColorByObject(string $orderId, int $quantity, string $object): string
    {
        switch ($object) {
            case 'packages':
                return $this->getColor((new OrderPackageService())->getStatusesDone($orderId) === $quantity);
            case 'complains':
                return $this->getColor((new ComplainService())->getStatusesDone($orderId) === $quantity);
            // Deliveries
            default:
                return $this->getColor((new DeliveryService())->getStatusesDone($orderId) === $quantity);
        }
    }

    /**
     * @param  bool  $isDone
     * @return string
     */
    private function getColor(bool $isDone): string
    {
        $red = ColorConstant::RED;
        $green = ColorConstant::GREEN;
        if ($isDone) {
            return $green;
        }
        return $red;
    }

    /**
     * @param  Collection  $result
     * @param  float  $exchangeRate
     * @param  bool  $isInspection
     * @return array
     */
    private function getSuppliers(Collection $result, float $exchangeRate, bool $isInspection): array
    {
        $data = [];
        foreach ($result as $supplierId => $products) {
            /** @var Collection $orderSupplier */
            $orderSupplier = OrderSupplier::query()->firstOrCreate(
                ['order_id' => $orderId = $products->first()->order_id, 'supplier_id' => $supplierId]
            );
            if (!$orderSupplier->total_amount) {
                (new OrderService())->updateOrderSupplier(Order::query()->find($orderId));
            }
            $tmp = [
                'id' => $supplierId,
                'name' => optional(Supplier::query()->find($supplierId))->name,
                'products' => $products->toArray(),
                'exchange_rate' => (float)$exchangeRate,
                'is_inspection' => $orderSupplier->is_inspection,
                'is_woodworking' => $orderSupplier->is_inspection,
                'is_insurance' => $orderSupplier->is_insurance,
                'is_shock_proof' => $orderSupplier->is_shock_proof,
                'weight' => $products->sum('weight'),
                'volume' => $products->sum('volume'),
                'order_cost' => $orderSupplier->order_cost,
                'order_fee' =>$orderSupplier->order_fee,
                'inspection_cost' => $orderSupplier->inspection_cost,
                'discount_cost' => $orderSupplier->discount_cost,
                'international_shipping_cost' => $orderSupplier->international_shipping_cost,
                'china_shipping_cost' => $orderSupplier->china_shipping_cost,
                'total_amount' => $orderSupplier->total_amount
            ];
            $data[] = $tmp;
        }
        return $data;
    }
}
