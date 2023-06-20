<?php

namespace App\Observers;

use App\Constants\CustomerConstant;
use App\Constants\PackageConstant;
use App\Models\OrderPackage;
use App\Models\OrderPackageStatusTime;
use App\Models\ReportPackage;
use App\Services\OrderService;
use App\Services\ReportCustomerService;
use Illuminate\Support\Facades\Auth;

class OrderPackageObserve
{
    private ReportCustomerService $_reportCustomerService;

    public function __construct(ReportCustomerService $reportCustomerService)
    {
        $this->_reportCustomerService = $reportCustomerService;
    }

    /**
     * Handle the OrderPackage "created" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function created(OrderPackage $orderPackage)
    {
        (new OrderService())->incrementByColumn($orderPackage->order_id, 'packages_number');
        OrderPackageStatusTime::query()->create(
            ['key' => PackageConstant::STATUS_PENDING, 'order_package_id' => $orderPackage->id]
        );
        ReportPackage::query()->increment(PackageConstant::STATUS_PENDING);
        $this->_reportCustomerService->incrementByKey(CustomerConstant::KEY_REPORT_PACKAGE);
    }

    /**
     * Handle the OrderPackage "updated" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function updated(OrderPackage $orderPackage)
    {
        //
    }

    /**
     * Handle the OrderPackage "deleted" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function deleted(OrderPackage $orderPackage)
    {
        $this->_reportCustomerService->decrementByKey(CustomerConstant::KEY_REPORT_PACKAGE);
    }

    /**
     * Handle the OrderPackage "restored" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function restored(OrderPackage $orderPackage)
    {
        //
    }

    /**
     * Handle the OrderPackage "force deleted" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function forceDeleted(OrderPackage $orderPackage)
    {
        //
    }
}
