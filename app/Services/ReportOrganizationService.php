<?php


namespace App\Services;


use App\Models\ReportOrganizationOrder;
use Illuminate\Support\Facades\Auth;

class ReportOrganizationService implements Service
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
        $this->getReportOrganizationCurrent()->increment($key, $amount);
    }

    /**
     * @param  string  $key
     * @param  float|null  $amount
     */
    public function decrementByKey(string $key, ?float $amount = 1): void
    {
        $report = $this->getReportOrganizationCurrent();
        if ($report->{$key} <= $amount) {
            $report->{$key} = 0;
            $report->save();
        } else {
            $this->getReportOrganizationCurrent()->decrement($key, $amount);
        }
    }

    public function orderChangeStatus(string $statusOld, string $statusNew)
    {
        $this->incrementByKey($statusNew);
        $this->decrementByKey($statusOld);
    }

    public function getReportOrganizationCurrent()
    {
        return ReportOrganizationOrder::query()->firstOrCreate(['organization_id' => getOrganization()]);
    }


    /**
     * @param  string  $status
     */
    public function increaseOrderByStatus(string $status): void
    {
        $this->incrementByKey($status);
    }

    /**
     * @param  string  $status
     */
    public function decreaseOrderByStatus(string $status): void
    {
        $this->getReportOrganizationCurrent()->decrement($status);
    }
}