<?php

namespace App\Http\Controllers;

use App\Constants\CustomerConstant;
use App\Constants\TransactionConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\ConvertHelper;
use App\Helpers\PaginateHelper;
use App\Helpers\RandomHelper;
use App\Http\Requests\CustomerWithDrawalRequest;
use App\Http\Requests\Purchase\OrderPurchaseRequest;
use App\Http\Resources\Customer\CustomerWithdrawalResource;
use App\Http\Resources\PaginateJsonResource;
use App\Models\CustomerBank;
use App\Models\CustomerWithdrawal;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderSupplier;
use App\Services\AccountingService;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\ReportCustomerService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{
    private CustomerService $_customerService;
    private AccountingService $_accountingService;

    public function __construct()
    {
        $this->_customerService = new CustomerService();
        $this->_service = new TransactionService();
        $this->_accountingService = new AccountingService();
    }

    /**
     * @param  OrderPurchaseRequest  $request
     * @param  Order  $order
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function purchaseOrder(OrderPurchaseRequest $request, Order $order): JsonResponse
    {
        [$balanceAmount, $deposit] = $this->initPurchase();
        $amount = $this->_service->checkWallet(
            $orderCost = ($order->order_cost + $order->order_fee + $order->inspection_cost),
            $deposit,
            $balanceAmount
        );
        $order->deposit_percent = $deposit;
        $order->date_purchased = now();
        $order->save();
        if (!$this->_service->orderSplitAndPurchase($order, $deposit, $balanceAmount)) {
            $this->_service->purchaseOrder($order, $amount);
        }

        return resSuccess();
    }

    /**
     * @param  OrderPurchaseRequest  $request
     * @param  Order  $order
     * @param  string  $supplierId
     * @return JsonResponse
     * @throws \Throwable
     */
    public function purchaseOrderAndSupplier(
        OrderPurchaseRequest $request,
        Order $order,
        string $supplierId
    ): JsonResponse {
        DB::transaction(function() use($order, $supplierId) {
            [$balanceAmount, $deposit] = $this->initPurchase();
            $amount = AccountingHelper::getCosts(
                $this->getServiceCharge($order->id, $supplierId, $order->exchange_rate)
            );
            $amount = $this->_service->checkWallet($amount, $deposit, $balanceAmount);
            $newOrder = (new OrderService())->splitBySupplier($order->id, $supplierId);
            $this->_service->purchaseOrder($newOrder, $amount);
        });

        return resSuccess();
    }

    private function getServiceCharge(string $orderId, string $supplierId, float $exchangeRate): float
    {
        $condition = ['order_id' => $orderId, 'supplier_id' => $supplierId];
        $details = OrderDetail::query()->where($condition);
        $orderSupplier = OrderSupplier::query()->where($condition)->first();
        $amount = (clone $details)->sum('amount_cny') * $exchangeRate;
        $orderFee = $this->_accountingService->getOrderFee($amount);
        $inspection = optional($orderSupplier)->is_inspection ? $this->_accountingService->getInspectionCost(
            $details->count()
        ) : 0;
        if ($orderSupplier) {
            $orderSupplier->inspection_cost = $inspection;
            $orderSupplier->order_fee = $orderFee;
            $orderSupplier->save();
        }
        return $amount + $orderFee + $inspection;
    }

    /**
     * @return array
     */
    private function initPurchase(): array
    {
        $customerService = new CustomerService();
        $offer = $customerService->getCustomerOffer();
        $type = request()->input('type');
        return [
            $customerService->getBalanceAmount(),
            (bool)$type ? 100 : $offer['deposit']
        ];
    }

    /**
     * @param  CustomerWithDrawalRequest  $request
     * @return JsonResponse
     */
    public function withdrawal(CustomerWithDrawalRequest $request): JsonResponse
    {
        $params = $request->all();
        $params['balance'] = (new CustomerService())->getBalanceAmount() - $params['amount'];
        $params['customer_id'] = Auth::user()->id;
        $params['code'] = RandomHelper::getWithdrawalCode();
        $customerBank = CustomerBank::query()->find($params['customer_bank_id']);
        $params += $customerBank->only('account_holder', 'account_number', 'branch');
        $params['bank'] = optional($customerBank->bank)->name;
        $withdrawal = CustomerWithdrawal::query()->create($params);
        $this->_service->setTransaction(
            $withdrawal->amount,
            TransactionConstant::STATUS_WITHDRAWAL,
            trans(
                'transaction.withdrawal_done',
                ['code' => $withdrawal->code, 'amount' => ConvertHelper::numericToVND($withdrawal->amount)]
            ),
            false,
            $withdrawal
        );
        $reportCustomerService = (new ReportCustomerService());
        $reportCustomerService->incrementByKey(
            CustomerConstant::KEY_REPORT_WITHDRAWAL_AMOUNT,
            $withdrawal->amount
        );
        $reportCustomerService->decrementByKey(
            CustomerConstant::KEY_REPORT_PURCHASE_AMOUNT,
            $withdrawal->amount
        );
        return resSuccessWithinData($withdrawal);
    }

    /**
     * @return JsonResponse
     */
    public function getWithdrawal(): JsonResponse
    {
        return resSuccessWithinData(
            new PaginateJsonResource(
                CustomerWithdrawal::query()->paginate(
                    PaginateHelper::getPerPage()
                ),
                CustomerWithdrawalResource::class
            )
        );
    }

    /**
     * @param  CustomerWithdrawal  $customerWithdrawal
     * @return JsonResponse
     * @throws \Throwable
     */
    public function withdrawalCancel(CustomerWithdrawal $customerWithdrawal): JsonResponse
    {
        throwIfCustom(
            $customerWithdrawal->status !== CustomerConstant::KEY_WITHDRAWAL_STATUS_PENDING,
            trans('customer.withdrawal_not_cancel')
        );
        $customerWithdrawal->status = CustomerConstant::KEY_WITHDRAWAL_STATUS_CANCEL;
        $customerWithdrawal->save();

        $this->_service->setTransaction(
            $customerWithdrawal->amount,
            TransactionConstant::STATUS_REFUND,
            trans(
                'transaction.withdrawal_cancel',
                [
                    'name' => getCurrentUser()->name,
                    'amount' => ConvertHelper::numericToVND($customerWithdrawal->amount),
                    'code' => $customerWithdrawal->code,
                ]
            ),
            true,
            $customerWithdrawal
        );
        return resSuccess();
    }

    /**
     * @return JsonResponse
     */
    public function getTransactionType(): JsonResponse
    {
        $data = [];
        foreach (TransactionConstant::STATUSES as $key => $value) {
            $data[] = [
                'value' => $value,
                'key' => $key,
            ];
        }
        return resSuccessWithinData($data);
    }
}
