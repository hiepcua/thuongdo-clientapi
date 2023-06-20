<?php

namespace App\Http\Resources\Customer;

use App\Http\Resources\PaginateJsonResource;
use App\Http\Resources\Resource;
use App\Services\ReportCustomerService;

class CustomerWithdrawalPaginationResource extends PaginateJsonResource
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
        $report = (new ReportCustomerService())->getReportCustomerCurrent();
        $data['report'] = $report->only(
            'balance_amount',
            'order_amount',
            'deposited_amount',
            'withdrawal_amount',
            'purchase_amount',
            'discount_amount'
        );
        return $data;
    }
}
