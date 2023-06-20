<?php

namespace App\Http\Resources\Package;

use App\Constants\PackageConstant;
use App\Http\Resources\PaginateJsonResource;
use App\Http\Resources\Resource;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\ReportOrderVN;
use App\Services\ReportCustomerService;
use Illuminate\Support\Facades\Auth;

class PackagePaginationResource extends PaginateJsonResource
{
    public function __construct($resource, ?string $class = Resource::class)
    {
        parent::__construct($resource, $class);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        $data['reports'] = [];

        $data['status_warehouse_vn']['quantity'] = OrderPackage::query()->where(
            'status',
            PackageConstant::STATUS_WAREHOUSE_VN
        )->count();
        if (request()->query('status') !== PackageConstant::STATUS_WAREHOUSE_VN) {
            return $data;
        }
        $data['reports'] = $this->getCalculator();

        return $data;
    }

    private function getCalculator(): array
    {
        $data = [];
        $reports = ReportOrderVN::query()->where(['customer_id' => Auth::user()->id])->get();
        $depositCost = 0;
        $orderCost = 0;
        $shipping = 0;
        $data['orders'] = [];
        foreach ($reports as $report) {
            $data['orders'][] = [
                'time' => $report->date_ordered,
                'code' => $report->order_code,
                'amount' => $report->order_amount,
                'deposit_cost' => $report->deposit_cost,
                'debt_cost' => $report->order_amount - $report->deposit_cost
            ];
            // Hàng liên quan đế ký gửi thì không có tiền hàng và tiền tạm ứng
            if($report->order instanceof Order) {
                $depositCost += $report->deposit_cost;
                $orderCost += $report->order_amount;
            }
            $shipping += $report->shipping_cost;
        }
        $data['orders_remainder_fee'] = $orderCost - $depositCost;
        $data['total_shipping'] = $shipping;
        $data['e_wallet'] = $eWallet = optional(
                (new ReportCustomerService())->getReportCustomerCurrent()
            )->balance_amount ?? 0;
        $balance = $eWallet - $data['orders_remainder_fee'] - $shipping;
        $data['balance'] = $balance > 0 ? $balance : 0;
        $data['recharge'] = $balance > 0 ? 0 : abs($balance);
        $data['status_warehouse_vn'] = count($data['orders']);
        return $data;
    }
}
