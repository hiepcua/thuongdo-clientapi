<?php


namespace App\Services;


use App\Constants\CustomerConstant;
use App\Constants\DeliveryConstant;
use App\Constants\TransactionConstant;
use App\Helpers\ConvertHelper;
use App\Helpers\PaginateHelper;
use App\Helpers\RandomHelper;
use App\Http\Resources\Delivery\DeliveryResource;
use App\Http\Resources\ListResource;
use App\Models\CustomerDelivery;
use App\Models\Delivery;
use App\Models\DeliveryOrder;
use App\Models\Order;
use App\Models\OrderPackage;
use Illuminate\Http\JsonResponse;

class DeliveryService extends BaseService
{
    protected string $_resource = DeliveryResource::class;

    /**
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getListByOrderId(string $orderId): JsonResponse
    {
        $complains = Delivery::query()->where('order_id', $orderId)->limit(PaginateHelper::getLimit())->get();
        return resSuccessWithinData((new ListResource($complains, $this->_resource)));
    }

    /**
     * @param  array  $params
     * @return Delivery
     */
    public function storeByOrder(array $params): Delivery
    {
        /** @var CustomerDelivery $customDelivery */
        $customDelivery = CustomerDelivery::query()->select(
            'receiver',
            'address',
            'phone_number',
            'delivery_cost',
            'province_id',
            'district_id',
            'ward_id',
            'customer_id'
        )->findOrFail(
            $params['customer_delivery_id']
        )->toArray();
        $customDelivery['address'] = $customDelivery['address_only'];
        $params += $customDelivery;
        $packages = OrderPackage::query()->find($params['packages']);
        $order = optional($packages->first())->order;
        $params['code'] = RandomHelper::getDeliveryCode();
        $params['is_delivery_cost_paid'] = $params['delivery_cost'] > 0;
        $params['shock_proof_cost'] = $packages->sum('shock_proof_cost');
        $params['storage_cost'] = $packages->sum('storage_cost');
        $params['woodworking_cost'] = $packages->sum('woodworking_cost');
        $params['inspection_cost'] = $packages->sum('inspection_cost');
        $params['insurance_cost'] = $packages->sum('insurance_cost');
        $params['international_shipping_cost'] = $packages->sum('international_shipping_cost');
        $params['china_shipping_cost'] = $packages->sum('china_shipping_cost');
        $params['order_id'] = optional($order)->id;
        $params['order_type'] = get_class($order);

        /** @var Delivery $delivery */
        $delivery = Delivery::query()->create($params);
        OrderPackage::query()->whereIn('id', $params['packages'])->update(
            ['delivery_id' => $delivery->id, 'is_delivery' => true]
        );

        foreach ($packages as $package)
        {
            $order = $package->order;
            DeliveryOrder::query()->create(
                [
                    'delivery_id' => $delivery->id,
                    'order_type' => get_class($order),
                    'order_id' =>optional($order)->id,
                ]
            );
        }

        $this->setLogs($delivery, $packages);

        return $delivery;
    }

    /**
     * @param  Delivery  $delivery
     * @param $packages
     */
    private function setLogs(Delivery $delivery, $packages)
    {
        foreach ($packages as $package) {
            $order = $package->order;
            $order->update(['delivery_id' => $delivery->id]);
            if ($order instanceof Order) {
                (new ActivityService())->setOrderLog($delivery, trans("activity.order_delivery"), $order->id);
                (new OrderService())->incrementByColumn($order->id, 'deliveries_number');
                continue;
            }
           
            (new ActivityService())->setConsignmentLog($delivery, trans("activity.order_delivery"), $order->id);
            (new ConsignmentService())->incrementByColumn($order->id, 'deliveries_number');
        }
    }

    public function destroy(string $id): JsonResponse
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::query()->findOrFail($id);
        if ($delivery->status !== DeliveryConstant::KEY_STATUS_PENDING) {
            return resError('system.can_not_delete');
        }
        if ($delivery->payment === DeliveryConstant::PAYMENT_E_WALLET) {
            $reportCustomer = new ReportCustomerService();
            $reportCustomer->decrementByKey(
                CustomerConstant::KEY_REPORT_ORDER_COST,
                $delivery->debt_cost
            );
            $reportCustomer->incrementByKey(
                CustomerConstant::KEY_REPORT_DISCOUNT_AMOUNT,
                $delivery->amount
            );
            (new TransactionService())->setTransaction(
                $delivery->amount,
                TransactionConstant::STATUS_REFUND,
                trans(
                    'transaction.delivery_refund',
                    [
                        'amount' => ConvertHelper::numericToVND($delivery->amount),
                        'bill' => implode(',', optional($delivery->packages)->pluck('bill_code')->all())
                    ]
                ),
                true
            );
        }
        OrderPackage::query()->where('delivery_id', $id)->update(['delivery_id' => null, 'is_delivery' => false]);
        return parent::destroy($id);
    }

    /**
     * @param  string  $orderId
     * @return int
     */
    public function getStatusesDone(string $orderId): int
    {
        return Delivery::query()->join('orders', 'orders.delivery_id', '=', 'deliveries.id')->where(
            ['orders.id' => $orderId, 'deliveries.status' => DeliveryConstant::KEY_STATUS_DONE]
        )->count();
    }
}