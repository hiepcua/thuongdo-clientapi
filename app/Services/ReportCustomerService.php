<?php


namespace App\Services;


use App\Constants\CustomerConstant;
use App\Models\ReportCustomer;
use Illuminate\Support\Facades\Auth;

class ReportCustomerService implements Service
{
    private OrderService $_orderService;

    public function __construct()
    {
        $this->_orderService = new OrderService();
    }

    /**
     * @param  string  $key
     * @param  int|null  $amount
     */
    public function incrementByKey(string $key, ?int $amount = 1): void
    {
        $this->getReportCustomerCurrent()->increment($key, $amount);
    }

    /**
     * @param  string  $key
     * @param  float|null  $amount
     */
    public function decrementByKey(string $key, ?float $amount = 1): void
    {
        $report = $this->getReportCustomerCurrent();
        if ($report->{$key} <= $amount) {
            $report->{$key} = 0;
            $report->save();
        } else {
            $this->getReportCustomerCurrent()->decrement($key, $amount);
        }
    }

    public function changeStatus(string $statusOld, string $statusNew)
    {
        $this->incrementByKey($statusNew);
        $this->decrementByKey($statusOld);
    }

    public function getReportCustomerCurrent()
    {
        return ReportCustomer::query()->firstOrCreate(['customer_id' => getCurrentUserId()]);
    }

    /**
     * Update level
     */
    public function updateLevel(): void
    {
        $level = $this->getLevel();
        (new ReportService())->reportLevel($level, Auth::user()->level);
        Auth::user()->update(['level' => $level]);
    }

    /**
     * Get level current customer
     * @return int
     */
    public function getLevel(): int
    {
        $costs = optional($this->getReportCustomerCurrent())->{CustomerConstant::KEY_REPORT_ORDER_COST} ?? 0;
        return (int)((new ConfigService())->getLevelByCosts((float)$costs) ?? 0);
    }

    /**
     * @param  float  $amount
     */
    public function increaseOrderAmount(float $amount): void
    {
        $this->getReportCustomerCurrent()->increment('order_amount', $amount);
    }

    /**
     * @param  float  $amount
     */
    public function balanceAmountDecrease(float $amount): void
    {
        $this->decrementByKey(CustomerConstant::KEY_REPORT_BALANCE_AMOUNT, $amount);
        $this->incrementByKey(CustomerConstant::KEY_REPORT_PURCHASE_AMOUNT, $amount);
    }

    /**
     * @param  float  $amount
     */
    public function balanceAmountIncrease(float $amount): void
    {
        $this->incrementByKey(CustomerConstant::KEY_REPORT_BALANCE_AMOUNT, $amount);
        $this->decrementByKey(CustomerConstant::KEY_REPORT_PURCHASE_AMOUNT, $amount);
    }
}