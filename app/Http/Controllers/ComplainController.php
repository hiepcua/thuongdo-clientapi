<?php

namespace App\Http\Controllers;

use App\Constants\ComplainConstant;
use App\Http\Resources\Complain\ComplainDetailResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\OnlyIdNameResource;
use App\Http\Resources\ReportStatusResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Models\Complain;
use App\Models\ComplainType;
use App\Models\Order;
use App\Models\ReportComplain;
use App\Models\ReportUserComplainCustomer;
use App\Models\ReportUserOrderedCustomer;
use App\Models\ReportUserTakeCareCustomer;
use App\Models\Solution;
use App\Services\ComplainService;
use App\Services\OrderDetailService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ComplainController extends Controller implements StoreValidationInterface
{
    public function __construct(ComplainService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getListByOrderId(string $orderId): JsonResponse
    {
        return $this->_service->getListByOrderId($orderId);
    }

    /**
     * @return JsonResponse
     */
    public function getTypes(): JsonResponse
    {
        return resSuccessWithinData(new ListResource(ComplainType::query()->get(), OnlyIdNameResource::class));
    }

    /**
     * @return JsonResponse
     */
    public function getSolutions(): JsonResponse
    {
        return resSuccessWithinData(new ListResource(Solution::query()->get(), OnlyIdNameResource::class));
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'complain_type_id' => 'required|uuid|exists:complain_type,id',
            'solution_id' => 'required|uuid|exists:solutions,id',
            'products' => 'required|array',
            'products.*.order_detail_id' => 'required|uuid|exists:order_details,id',
            'products.*.images' => 'array',
            'products.*.complain_note' => 'string',
            'images_bill' => 'required|array',
            'images_bill.*' => 'required|string',
            'images_received' => 'required|array',
            'images_received.*' => 'required|string',
        ];
    }

    public function store(): JsonResponse
    {
        $userService = new UserService();
        $this->throwValidationAndAction(__FUNCTION__);
        $params = request()->all();
        $params['order_id'] = $orderId = request()->orderId;
        $order = Order::query()->find($orderId);
        $params['organization_id'] = Auth::user()->organization_id;
        $params['customer_id'] = Auth::user()->id;
        $params['staff_order_id'] = $order->staff_order_id ?? $userService->getStaffAssign(
                ReportUserOrderedCustomer::class
            );
        $params['staff_complain_id'] = $userService->getStaffAssign(ReportUserComplainCustomer::class);
        $params['staff_care_id'] = $order->staff_care_id ?? $userService->getStaffAssign(
                ReportUserTakeCareCustomer::class
            );
        $params['customer_id'] = $order->customer_id;
        /** @var Complain $complain */
        $complain = Complain::query()->create($params);
        (new OrderDetailService())->insertComplainImage($params, $complain);
        $this->_service->insertImages($complain->id, $params['images_bill'], $params['images_received']);
        return resSuccessWithinData($complain);
    }

    public function detail(string $id): JsonResponse
    {
        return resSuccessWithinData(new ComplainDetailResource(Complain::query()->findOrFail($id)));
    }

    /**
     * @return JsonResponse
     */
    public function reportStatus(): JsonResponse
    {
        $columns = array_filter(array_keys(ComplainConstant::STATUSES));
        reset($columns);
        $reports = ReportComplain::query()->firstOrCreate(['organization_id' => getOrganization(), 'customer_id' => getCurrentUserId()]);
        $reports = ReportComplain::query()->select(
            $columns
        )->first()->toArray();
        $data = [];
        foreach ($reports as $key => $report) {
            $data[] = new ReportStatusResource($key, ComplainConstant::class, $report);
        }
        return resSuccessWithinData($data);
    }

    /**
     * @param  Complain  $complain
     * @return JsonResponse
     */
    public function statusCancel(Complain $complain): JsonResponse
    {
        if ($complain->status !== ComplainConstant::KEY_STATUS_PENDING) {
            return resError(trans('complain.can_not_cancel'));
        }
        $complain->status = ComplainConstant::KEY_STATUS_CANCEL;
        $complain->save();
        return resSuccess();
    }
}
