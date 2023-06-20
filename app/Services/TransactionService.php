<?php


namespace App\Services;


use App\Constants\CustomerConstant;
use App\Constants\OrderConstant;
use App\Constants\TransactionConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\ConvertHelper;
use App\Helpers\PaginateHelper;
use App\Helpers\RandomHelper;
use App\Http\Resources\Customer\CustomerWithdrawalPaginationResource;
use App\Http\Resources\TransactionResource;
use App\Models\Consignment;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService implements Service
{
    private ReportCustomerService $_reportCustomer;

    public function __construct()
    {
        $this->_reportCustomer = new ReportCustomerService();
    }

    /**
     * @param  Order  $order
     * @param  float  $amount
     */
    public function purchaseOrder(Order $order, float $amount): void
    {
        $this->setBalanceToCache();
        DB::transaction(
            function () use ($order, $amount) {
                $isPurchaseAll = $amount == $order->total_amount;
                $order->deposit_cost = $amount;
                $order->deposit_percent = $isPurchaseAll ? 100 : $order->deposit_percent ;
                $order->status = OrderConstant::KEY_STATUS_DEPOSITED;
                $order->save();

                $this->setTransaction(
                    $amount,
                    TransactionConstant::STATUS_PURCHASE,
                    trans(
                        $isPurchaseAll ? 'transaction.purchase_order_cost' : 'transaction.order_deposit',
                        [
                            'code' => $order->code,
                            'amount' => ConvertHelper::numericToVND($amount),
                            'percent' => $order->deposit_percent.'%'
                        ]
                    ),
                    false
                );
                $this->_reportCustomer->incrementByKey(CustomerConstant::KEY_REPORT_ORDER_COST, $amount);
                $this->_reportCustomer->updateLevel();
            }
        );
    }


    /**
     * @param  Order  $order
     * @param  float  $deposit
     * @param  float  $balanceAmount
     * @return bool
     * @throws \Throwable
     */
    public function orderSplitAndPurchase(Order $order, float $deposit, float $balanceAmount): bool
    {
        $suppliers = OrderDetail::query()->where('order_id', $order->id)->groupBy('supplier_id')->get();
        $hasSplit = count($suppliers) > 1;
        if (!$hasSplit) {
            return false;
        }
        DB::transaction(
            function () use ($order, $suppliers, $deposit, $balanceAmount) {
                foreach ($suppliers as $supplier) {
                    $newOrder = (new OrderService())->splitBySupplier($order->id, $supplier->supplier_id);
                    $amount = $this->checkWallet($newOrder->total_amount, $deposit, $balanceAmount);
                    $this->purchaseOrder($newOrder, $amount);
                }
            }
        );

        return true;
    }

    /**
     * @param  float  $cost
     * @param  float  $deposit
     * @param  float  $balanceAmount
     * @return float
     * @throws \Throwable
     */
    public function checkWallet(float $cost, float $deposit, float $balanceAmount): float
    {
        $amount = AccountingHelper::getCosts($cost * $deposit / 100);
        enoughMoneyToPay($balanceAmount < $amount);
        return $amount;
    }


    public function purchaseDelivery(Delivery $delivery, array $params): void
    {
        $this->setBalanceToCache();
        DB::transaction(
            function () use ($delivery, $params) {
                $amount = $delivery->amount;
                if ($amount === 0) {
                    return;
                }
                $this->setDeliveryTransaction($delivery);
                $this->_reportCustomer->increaseOrderAmount($delivery->debt_cost);
                $this->_reportCustomer->updateLevel();
            }
        );
    }

    public function setDeliveryTransaction(Delivery $delivery): void
    {
        $array = [
            'debt_cost' => 'delivery_debt_cost',
            'shipping_cost' => 'delivery_shipping_cost',
            'delivery_cost' => 'delivery_cost'
        ];
        $packages = optional($delivery->packages);
        $ordersPackage = optional($packages)->where('order_type', Order::class);
        $consignmentsPackage = optional($packages)->where('order_type', Consignment::class);
        $ordersCode = $this->getValuesByArrayAndColumn($ordersPackage, 'order_code');
        $consignmentsCode = $this->getValuesByArrayAndColumn($consignmentsPackage, 'order_code');
        foreach ($array as $key => $msg) {
            $amount = $delivery->{$key};
            if ($amount <= 0) {
                continue;
            }

            if ($key === 'shipping_cost') {
                $orders = $ordersCode;
            } else {
                $orders = array_unique(array_merge($ordersCode, $consignmentsCode));
            }

            $params = [
                'amount' => ConvertHelper::numericToVND($amount),
            ];

            if ($key !== 'debt_cost') {
                $params['bill'] = implode(
                    ',',
                    $this->getValuesByArrayAndColumn(
                        $key == 'shipping_cost' ? $ordersPackage : $packages,
                        'bill_code'
                    )
                );
            } else {
                $orders = $this->getOrders($ordersCode);
            }

            $params['code'] = implode(',', $orders);

            $msg = trans(
                "transaction.$msg",
                $params
            );
            $this->setTransaction(
                $amount,
                TransactionConstant::STATUS_PURCHASE,
                $msg,
                false,
                $delivery
            );
        }
    }

    private function getValuesByArrayAndColumn($object, string $column): array
    {
        return optional($object)->pluck($column)->all();
    }

    private function getOrders(array $orders): array
    {
        return Order::query()->whereIn('code', $orders)->whereRaw('order_cost > deposit_cost')->get()->pluck(
            'code'
        )->all();
    }

    /**
     * @param  float  $amount
     * @param  string  $status
     * @param  string  $content
     * @param  bool  $isIncrement
     * @param  null  $sourceable
     */
    public function setTransaction(
        float $amount,
        string $status,
        string $content,
        bool $isIncrement,
        $sourceable = null
    ) {
        $balance = (new CustomerService())->getBalanceAmount();
        $result = $balance + $amount;
        if ($isIncrement) {
            $this->_reportCustomer->balanceAmountIncrease($amount);
        } else {
            $this->_reportCustomer->balanceAmountDecrease($amount);
            $result = $balance - $amount;
        }
        Transaction::query()->create(
            [
                'customer_id' => $customer = Auth::user()->id,
                'sourceable_type' => $sourceable ? get_class($sourceable) : null,
                'sourceable_id' => $sourceable ? $sourceable->id : null,
                'amount' => $amount,
                'time' => now(),
                'status' => $status,
                'content' => $content,
                'code' => RandomHelper::getTransactionCode(),
                'balance' => $result ,
                'organization_id' => Auth::user()->organization_id
            ]
        );
    }

    public function pagination(): JsonResponse
    {
        $data = Transaction::query()->paginate(PaginateHelper::getPerPage());
        return resSuccessWithinData(new CustomerWithdrawalPaginationResource($data, TransactionResource::class));
    }

    public function setBalanceToCache()
    {
        (new CustomerService())->getBalanceAmount();
    }
}