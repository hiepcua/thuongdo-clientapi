<?php

namespace App\Http\Controllers;

use App\Constants\OrderConstant;
use App\Http\Resources\ListResource;
use App\Http\Resources\Order\OrderProductResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ReportCustomer;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller implements StoreValidationInterface
{
    public function __construct(OrderService $service)
    {
        $this->_service = $service;
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'delivery_type' => 'required|in:normal,fast',
            'is_inspection' => 'required|boolean',
            'is_woodworking' => 'required|boolean',
            'is_shock_proof' => 'required|boolean',
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_delivery_id' => 'required|exists:customer_deliveries,id',
            'products' => 'required|array',
            'products.*.name' => 'required|max:255',
            'products.*.url' => 'required|url|max:255',
            'products.*.image' => 'required|string|max:255',
            'products.*.classification' => 'string|max:255',
            'products.*.unit_price_cny' => 'required|min:0|numeric',
            'products.*.quantity' => 'required|min:1|numeric',
            'products.*.supplier' => 'required|max:255|string',
            'products.*.category_id' => 'required|uuid|exists:categories,id',
        ];
    }

    /**
     * @return JsonResponse
     */
    public function reportStatus(): JsonResponse
    {
        return resSuccessWithinData(
            $this->_service->getReportsHasQuantity((new ReportCustomer())->getTable(), OrderConstant::class)
        );
    }

    /**
     * @param Order $order
     * @return JsonResponse
     */
    public function cancel(Order $order): JsonResponse
    {
        if ($res = $this->_service->msgUpdatingNotAllow($order->status)) {
            return $res;
        }
        $order->status = OrderConstant::KEY_STATUS_CANCEL;
        $order->save();
        return resSuccess();
    }

    /**
     * @param string $code
     * @return JsonResponse
     */
    public function getProducts(string $code): JsonResponse
    {
        $orderDetail = OrderDetail::query()->whereHas(
            'order',
            function ($q) use ($code) {
                return $q->where('code', $code);
            }
        )->with('orderPackage')->get();
        return resSuccessWithinData(new ListResource($orderDetail, OrderProductResource::class));
    }

    /**
     * Tách đơn
     * @param  string  $orderId
     * @param  string  $supplierId
     * @return JsonResponse
     */
    public function orderSplitBySupplier(string $orderId, string $supplierId): JsonResponse
    {
        $this->_service->splitBySupplier($orderId, $supplierId);
        return resSuccess();
    }

    /**
     * @param  string  $ids
     * @return JsonResponse
     */
    public function getDebt(string $ids): JsonResponse
    {
        return resSuccessWithinData((new OrderService())->getDebtCost(explode(',', $ids)));
    }


}
