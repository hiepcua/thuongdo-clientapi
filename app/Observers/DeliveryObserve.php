<?php

namespace App\Observers;

use App\Constants\ActivityConstant;
use App\Constants\DeliveryConstant;
use App\Http\Resources\ReportStatusResource;
use App\Models\Delivery;
use App\Models\ReportDelivery;
use App\Services\ActivityService;
use App\Services\ReportService;
use App\Services\Service;

class DeliveryObserve
{
    private Service $_activityService;

    public function __construct()
    {
        $this->_activityService = new ActivityService();
    }

    /**
     * Handle the Delivery "created" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function created(Delivery $delivery)
    {
        (new ReportService())->incrementByOrganization(ReportDelivery::class, DeliveryConstant::KEY_STATUS_PENDING);
    }

    /**
     * Handle the Delivery "updated" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function updated(Delivery $delivery)
    {
        if (($old = $delivery->getOriginal('status')) != $delivery->status) {
            (new ReportService())->inDecrementByOrganization(ReportDelivery::class, $old, $delivery->status);
            (new ActivityService())->setLog(
                $delivery,
                DeliveryConstant::STATUSES[$delivery->status],
                ActivityConstant::DELIVERY_STATUS,
                null,
                json_encode(
                    [
                        'status' => new ReportStatusResource(
                            $delivery->status,
                            DeliveryConstant::class
                        )
                    ]
                )
            );
        }
    }

    /**
     * Handle the Delivery "deleted" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function deleted(Delivery $delivery)
    {
        $delivery->order()->where('deliveries_number', '>', 0)->decrement('deliveries_number');
    }

    /**
     * Handle the Delivery "restored" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function restored(Delivery $delivery)
    {
        //
    }

    /**
     * Handle the Delivery "force deleted" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function forceDeleted(Delivery $delivery)
    {
        //
    }
}
