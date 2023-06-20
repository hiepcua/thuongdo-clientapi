<?php

namespace App\Observers;

use App\Constants\ConsignmentConstant;
use App\Constants\CustomerConstant;
use App\Models\Consignment;
use App\Models\ReportConsignment;
use App\Services\ActivityService;
use App\Services\ConsignmentService;
use App\Services\CustomerService;
use App\Services\ReportCustomerService;
use App\Services\ReportOrganizationService;

class ConsignmentObserve
{
    private ConsignmentService $_service;
    private ActivityService $_activityService;
    private ReportCustomerService $_reportCustomerService;
    private ReportOrganizationService $_reportOrganizationService;


    public function __construct(
        ConsignmentService $service,
        ActivityService $activeService,
        ReportCustomerService $reportCustomerService,
        ReportOrganizationService $organizationService
    ) {
        $this->_service = $service;
        $this->_activityService = $activeService;
        $this->_reportCustomerService = $reportCustomerService;
        $this->_reportOrganizationService = $organizationService;
    }

    /**
     * Handle the Consignment "created" event.
     *
     * @param  \App\Models\Consignment  $consignment
     * @return void
     */
    public function created(Consignment $consignment)
    {
        $this->_service->reportStatuses($consignment->id, ConsignmentConstant::KEY_STATUS_PENDING);
        if ($consignment->customer->level == 0) {
            (new CustomerService())->upgradeLevel($consignment->customer);
        }
        (new CustomerService())->setLastOrderAtByCurrentUser();
        $status = $consignment->status ?? ConsignmentConstant::KEY_STATUS_PENDING;
        $this->_activityService->setConsignment(
            $consignment,
            trans("activity.consignment_$status"),
            $consignment->id
        );
        $this->_reportCustomerService->incrementByKey(CustomerConstant::KEY_REPORT_CONSIGNMENT);
    }

    /**
     * Handle the Consignment "updated" event.
     *
     * @param  \App\Models\Consignment  $consignment
     * @return void
     */
    public function updated(Consignment $consignment)
    {
        if (($oldStatus = $consignment->getOriginal('status')) !== $consignment->status) {
            $this->_service->reportStatuses($consignment->id, $consignment->status, $oldStatus);
            $this->_activityService->setConsignment(
                $consignment,
                trans("activity.consignment_$consignment->status"),
                $consignment->id
            );
        }

    }

    /**
     * Handle the Consignment "deleted" event.
     *
     * @param  \App\Models\Consignment  $consignment
     * @return void
     */
    public function deleted(Consignment $consignment)
    {
        ReportConsignment::query()->where('consignment_id', $consignment->id)->decrement(
            "status_".$consignment->status
        );
        $this->_reportCustomerService->decrementByKey(CustomerConstant::KEY_REPORT_CONSIGNMENT);
    }

    /**
     * Handle the Consignment "restored" event.
     *
     * @param  \App\Models\Consignment  $consignment
     * @return void
     */
    public function restored(Consignment $consignment)
    {
        //
    }

    /**
     * Handle the Consignment "force deleted" event.
     *
     * @param  \App\Models\Consignment  $consignment
     * @return void
     */
    public function forceDeleted(Consignment $consignment)
    {
        //
    }
}
