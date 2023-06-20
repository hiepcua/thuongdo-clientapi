<?php


namespace App\Services;


use App\Constants\ConsignmentConstant;
use App\Constants\CustomerConstant;
use App\Helpers\PaginateHelper;
use App\Helpers\RandomHelper;
use App\Http\Resources\ConsignmentResource;
use App\Models\Consignment;
use App\Models\ConsignmentDetail;
use App\Models\ConsignmentStatusTime;
use App\Models\OrderPackage;
use App\Models\ReportConsignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ConsignmentService extends BaseService
{
    protected string $_resource = ConsignmentResource::class;

    public function pagination(int $perPage): JsonResponse
    {
        $data = Consignment::query()->paginate(PaginateHelper::getLimit());
        return resSuccessWithinData(new $this->_paginateResource($data, $this->_resource));
    }

    public function store(array $data)
    {
        $data['code'] = $code = RandomHelper::orderCode();
        $data['customer_id'] = $customer = Auth::user()->id;
        $data['warehouse_id'] = $data['warehouse_vi'];
        $data['date_ordered'] = now();
        $data['status'] = ConsignmentConstant::KEY_STATUS_PENDING;
        $packages = $data['packages'];
        $data['packages_number'] =  $packagesNumber = $this->getPackagesNumber($packages);
        (new ReportCustomerService())->incrementByKey(CustomerConstant::KEY_REPORT_PACKAGE, $packagesNumber);
        $consignment = parent::store($data);
        foreach ($packages as $key => $package) {
            $package['customer_id'] = $customer;
            $package['order_id'] = $consignment->id;
            $package['order_type'] = get_class($consignment);
            $package['order_code'] = $consignment->code;
            $package['insurance_cost'] = ($isInsurance = (bool)$package['is_insurance']) ? (new AccountingService(
            ))->getInsuranceCost($package['order_cost']) : 0;
            $package['is_insurance'] = $isInsurance;
            $package['inspection_cost'] = (bool)($package['is_inspection'] ?? false) ? (new AccountingService(
            ))->getInspectionCost($package['quantity']) : 0;
            $package['customer_delivery_id'] = $data['customer_delivery_id'];
            $package['warehouse_id'] = $data['warehouse_vi'];
            $package['warehouse_cn'] = $data['warehouse_cn'];
            $package['amount'] = $package['insurance_cost'];
            $package['exchange_rate'] = (new ConfigService())->getExchangeCost();
            $detail = $package;
            $detail['consignment_id'] = $consignment->id;
            $detail['order_package_id'] = OrderPackage::query()->create($package)->id;
            $detail['name'] = $detail['product_name'];
            ConsignmentDetail::query()->create($detail);
        }
        return $consignment;
    }

    /**
     * @param  array  $packages
     * @return mixed
     */
    private function getPackagesNumber(array $packages)
    {
        return collect($packages)->sum('packages_number');
    }

    /**
     * @param  array  $package
     * @return array
     */
    private function removeErrKeys(array $package): array
    {
        $except = ['image_path', 'key', 'loading', 'in_transit_name'];
        foreach ($package as $key => $item) {
            if (strpos($key, '_err') === false && array_search($key, $except) === false) {
                continue;
            }
            unset($package[$key]);
        }
        return $package;
    }

    /**
     * @param  string  $id
     * @param  string  $newStatus
     * @param  string|null  $oldStatus
     */
    public function reportStatuses(string $id, string $newStatus, ?string $oldStatus = null)
    {
        if (!$oldStatus) {
            ReportConsignment::query()->firstOrCreate(
                ['customer_id' => Auth::user()->id, 'organization_id' => getOrganization()]
            )->increment($newStatus);
        } else {
            ReportConsignment::query()->where($oldStatus, '>', 0)->decrement($oldStatus);
            ReportConsignment::query()->increment($newStatus);
        }
        ConsignmentStatusTime::query()->create(['consignment_id' => $id, 'key' => $newStatus]);
    }

    /**
     * @param  string  $orderId
     * @param  string  $column
     */
    public function incrementByColumn(string $orderId, string $column): void
    {
        optional(Consignment::query()->find($orderId))->increment($column);
    }
}