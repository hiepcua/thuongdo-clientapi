<?php


namespace App\Services;


use App\Constants\PackageConstant;
use App\Helpers\PaginateHelper;
use App\Helpers\StatusHelper;
use App\Http\Resources\ListResource;
use App\Http\Resources\Order\Package\OrderPackageResource;
use App\Http\Resources\Package\PackagePaginationResource;
use App\Models\OrderDetailPackage;
use App\Models\OrderPackage;
use App\Models\OrderPackageStatusTime;
use Illuminate\Http\JsonResponse;

class OrderPackageService extends BaseService
{
    protected string $_paginateResource = PackagePaginationResource::class;
    protected string $_resource = OrderPackageResource::class;

    public function index(): JsonResponse
    {
        $packages = OrderPackage::query()->get();
        return resSuccessWithinData(new ListResource($packages, $this->_resource));
    }

    public function pagination(int $perPage): JsonResponse
    {
        $packages = OrderPackage::query()->paginate(
            PaginateHelper::getPerPage()
        );
        return resSuccessWithinData(new $this->_paginateResource($packages, $this->_resource));
    }

    public function destroy(string $id): JsonResponse
    {
        if (OrderPackageStatusTime::query()->where(
            ['order_package_id' => $id, 'key' => PackageConstant::STATUS_IN_PROGRESS]
        )->exists()) {
            return resError('package.can_not_delete');
        }
        OrderPackage::query()->where('delivery_id', $id)->update(['is_delivery', false]);
        return parent::destroy($id);
    }

    /**
     * @param  string  $key
     * @return string[]
     */
    public function getStatus(string $key): array
    {
        return StatusHelper::getInfo($key, PackageConstant::class) + ['value' => $key];
    }

    /**
     * @param  string  $key
     * @return int
     */
    private function getIndexKey(string $key): int
    {
        $array = explode('_', $key);
        return (int)array_pop($array);
    }

    /**
     * Xóa các bản ghi có liên quan đến đơn hàng và ký gửi
     * @param  string  $id
     */
    public function deleteByOrderId(string $id)
    {
        foreach (OrderPackage::query()->where('order_id', $id)->cursor() as $package) {
            $package->delete();
        }
    }

    /**
     * @param  string  $orderId
     * @return int
     */
    public function getStatusesDone(string $orderId): int
    {
        return OrderPackage::query()->where(
            ['order_id' => $orderId, 'status' => PackageConstant::STATUS_DELIVERED]
        )->count();
    }

    public function getPackageByOrderDetailId($orderDetailId)
    {
        return
            OrderPackage::query()->find(
                optional(
                    OrderDetailPackage::query()->where('order_detail_id', $orderDetailId)->first()
                )->order_package_id
            );
    }

    public function getBillCodesByIds(array $ids): string
    {
        return implode(',', optional(OrderPackage::query()->find($ids))->pluck('bill_code')->all() ?? []);
    }
}