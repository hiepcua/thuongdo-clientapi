<?php

namespace App\Observers;

use App\Constants\ComplainConstant;
use App\Models\Complain;
use App\Models\ComplainStatusTime;
use App\Models\ReportComplain;
use App\Services\ActivityService;
use App\Services\OrderService;
use App\Services\ReportService;
use App\Services\Service;

class ComplainObserve
{
    private Service $_activityService;

    public function __construct()
    {
        $this->_activityService = new ActivityService();
    }

    /**
     * Handle the Complain "created" event.
     *
     * @param  \App\Models\Complain  $complain
     * @return void
     */
    public function created(Complain $complain)
    {
        ReportComplain::query()->firstOrCreate(['organization_id' => getOrganization(), 'customer_id' => $complain->customer_id])->increment(ComplainConstant::KEY_STATUS_PENDING);
        $this->_activityService->setOrderLog($complain, trans("activity.order_complain"), $complain->order_id);
        (new OrderService())->incrementByColumn($complain->order_id, 'complains_number');
        ComplainStatusTime::query()->create(['complain_id' => $complain->id, 'key' => "status_$complain->status"]);
    }

    /**
     * Handle the Complain "updated" event.
     *
     * @param  \App\Models\Complain  $complain
     * @return void
     */
    public function updated(Complain $complain)
    {
        (new ReportService())->updateReportWhenChangeStatus($complain, ReportComplain::class);
    }

    /**
     * Handle the Complain "deleted" event.
     *
     * @param  \App\Models\Complain  $complain
     * @return void
     */
    public function deleted(Complain $complain)
    {
        //
    }

    /**
     * Handle the Complain "restored" event.
     *
     * @param  \App\Models\Complain  $complain
     * @return void
     */
    public function restored(Complain $complain)
    {
        //
    }

    /**
     * Handle the Complain "force deleted" event.
     *
     * @param  \App\Models\Complain  $complain
     * @return void
     */
    public function forceDeleted(Complain $complain)
    {
        //
    }
}
