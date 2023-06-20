<?php

namespace App\Http\Controllers;

use App\Constants\DeliveryConstant;
use App\Http\Requests\Delivery\DeliveryStoreByOrderRequest;
use App\Models\CustomerDelivery;
use App\Models\OrderPackage;
use App\Services\CustomerService;
use App\Services\DeliveryService;
use App\Services\OrderService;
use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function __construct(DeliveryService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getListByOrder(string $orderId): JsonResponse
    {
        return $this->_service->getListByOrderId($orderId);
    }

    /**
     * @param  DeliveryStoreByOrderRequest  $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function storeByOrder(DeliveryStoreByOrderRequest $request): JsonResponse
    {
        $params = request()->all();
        $fees = [];
        $checkWallet = $this->checkWallet($fees, $params['packages'], $params['customer_delivery_id']);
        $params += $fees;
        DB::transaction(
            function () use ($params, $checkWallet) {
                $params['note_customer'] = $params['note'] ?? null;
                unset($params['note']);
                $packages = $params['packages'];
                $delivery = $this->_service->storeByOrder($params);
                if ($params['payment'] === DeliveryConstant::PAYMENT_E_WALLET) {
                    enoughMoneyToPay($checkWallet);
                    (new TransactionService())->purchaseDelivery($delivery, $packages);
                }
            }
        );
        return resSuccess();
    }

    /**
     * @param  array  $params
     * @param  array  $packages
     * @param  string  $customerDeliveryId
     * @return bool
     */
    private function checkWallet(array &$params, array $packages, string $customerDeliveryId): bool
    {
        $packages = OrderPackage::query()->findMany($packages);
        $delivery = optional(CustomerDelivery::query()->findOrFail($customerDeliveryId))->delivery_cost ?? 0;
        $debt = $this->getDebtCostByOrder($packages);
        $shipping = $packages->sum('shipping_cost');
        $params = ['delivery_cost' => $delivery, 'debt_cost' => $debt, 'shipping_cost' => $shipping];
        return (new CustomerService())->getBalanceAmount() < ($delivery + $debt + $shipping);
    }

    /**
     * @param  Collection  $packages
     * @return float
     */
    public function getDebtCostByOrder(Collection $packages): float
    {
        $debt = 0;
        $orderIds = $packages->pluck('order_id')->all();
        $packages = $packages->pluck('id')->all();
        foreach (array_unique($orderIds) as $orderId) {
            if (!OrderPackage::query()->where(['order_id' => $orderId])->whereNotIn('id', $packages)->whereNull('delivery_id')->exists()) {
                $debt += (new OrderService())->getDebtCost([$orderId]);
            }
        }
        return $debt;
    }
}
