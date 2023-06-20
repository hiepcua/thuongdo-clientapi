<?php

namespace App\Http\Controllers;

use App\Constants\PackageConstant;
use App\Http\Requests\OrderPackageNoteRequest;
use App\Http\Resources\ConsignmentProductResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\Order\OrderProductResource;
use App\Http\Resources\Order\Package\OrderPackageResource;
use App\Http\Resources\Order\Package\SupplierResource;
use App\Models\ConsignmentDetail;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Scopes\OrganizationScope;
use App\Services\ActivityService;
use App\Services\OrderPackageService;
use Illuminate\Http\JsonResponse;

class OrderPackageController extends Controller
{
    public function __construct(OrderPackageService $service)
    {
        $this->_service = $service;
    }

    /**
     * @return JsonResponse
     */


    /**
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getPackageByOrderId(string $orderId): JsonResponse
    {
        $packages = OrderPackage::query()->where('order_id', $orderId)->get();
        return resSuccessWithinData(new ListResource($packages, OrderPackageResource::class));
    }

    /**
     * @param  string  $deliveryId
     * @return JsonResponse
     */
    public function getPackageByDeliveryId(string $deliveryId): JsonResponse
    {
        $packages = OrderPackage::query()->where('delivery_id', $deliveryId)->get();
        return resSuccessWithinData(new ListResource($packages, OrderPackageResource::class));
    }

    /**
     * @param  OrderPackageNoteRequest  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function addNote(OrderPackageNoteRequest $request, string $id): JsonResponse
    {
        /** @var OrderPackage $package */
        $package = OrderPackage::query()->findOrFail($id);
        $package->note = $request->input('note');
        $package->save();
        (new ActivityService())->setOrderLog(
            $package,
            trans('activity.order_package_note', ['code' => $package->bill_code]),
            $package->order_id
        );
        return resSuccess();
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function getProductsById(string $id): JsonResponse
    {
        return $this->getProducts($id);
    }

    /**
     * @param  string  $ids
     * @return JsonResponse
     */
    public function getProductsByMultipleIds(string $ids): JsonResponse
    {
        return $this->getProducts($ids, OrderProductResource::class);
    }

    /**
     * @param  string  $id
     * @param  string|null  $resource
     * @return JsonResponse
     */
    public function getProducts(string $id, ?string $resource = SupplierResource::class): JsonResponse
    {
        $ids = explode(',', $id);
        $ids = array_filter($ids);
        $details = [];
        OrderPackage::query()->findMany($ids)->each(function($item) use (&$details) {
            $details = array_merge($details, $item->orderDetails->pluck('id')->all());
        });
        $details = OrderDetail::query()->findMany($details);
        if ($details->count() == 0) {
            $details = ConsignmentDetail::query()->whereIn('order_package_id', $ids)->get();
            $resource = ConsignmentProductResource::class;
        }
        if ($resource === SupplierResource::class) {
            $details = $details->groupBy('supplier_id')->values();
        }
        return resSuccessWithinData(
            new ListResource(
                $details,
                $resource
            )
        );
    }

    /**
     * @return JsonResponse
     */
    public function getStatuses(): JsonResponse
    {
        $data = [];
        foreach (PackageConstant::STATUSES as $key => $status) {
            $data[] = [
                'value' => $key,
                'name' => $status,
            ];
        }
        return resSuccessWithinData($data);
    }
}
