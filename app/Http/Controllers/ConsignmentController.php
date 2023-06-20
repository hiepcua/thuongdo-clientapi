<?php

namespace App\Http\Controllers;

use App\Constants\ConsignmentConstant;
use App\Constants\PackageConstant;
use App\Exceptions\CustomValidationException;
use App\Http\Requests\ConsignmentChangeStatusRequest;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Models\Consignment;
use App\Models\OrderPackage;
use App\Models\ReportConsignment;
use App\Services\ConsignmentService;
use Illuminate\Http\JsonResponse;

class ConsignmentController extends Controller implements StoreValidationInterface
{
    public function __construct(ConsignmentService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  ConsignmentChangeStatusRequest  $request
     * @param  Consignment  $consignment
     * @return JsonResponse
     * @throws \Throwable
     */
    public function changeStatus(ConsignmentChangeStatusRequest $request, Consignment $consignment): JsonResponse
    {
        $status = request()->input('status');
        throw_if(
            $consignment->status !== ConsignmentConstant::KEY_STATUS_PENDING && $status === ConsignmentConstant::KEY_STATUS_CANCEL,
            CustomValidationException::class,
            [trans('consignment.can_not_cancel')]
        );
        $consignment->update(['status' => $status]);
        OrderPackage::query()->where('order_id', $consignment->id)->each(
            function ($item) {
                $item->status = PackageConstant::STATUS_CANCEL;
                $item->save();
            }
        );
        return resSuccess();
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        $transit = array_keys(ConsignmentConstant::IN_TRANSIT);
        return [
            'packages' => 'required|array',
            'packages.*.image' => 'required|max:255',
            'packages.*.product_name' => 'required|max:255',
            'packages.*.bill_code' => ['required','max:50','unique:order_package,bill_code', function($attribute, $value, $fail) {
                if(collect(request()->input('packages'))->where('bill_code', $value)->count() > 1) {
                    $fail(trans('validation.distinct'));
                }
            }],
            'packages.*.packages_number' => 'required|numeric',
            'packages.*.transporter' => 'required|max:255',
            'packages.*.in_transit' => 'required|in:'.implode(',', $transit),
            'packages.*.category_id' => 'required|exists:categories,id',
            'packages.*.quantity' => 'numeric|min:0',
            'packages.*.order_cost' => 'required|numeric|min:0',
            'packages.*.description' => 'max:500',
            'packages.*.note' => 'max:500',
            'packages.*.is_insurance' => 'bool',
            'packages.*.is_inspection' => 'bool',
            'packages.*.is_woodworking' => 'bool',
            'packages.*.is_shock_proof' => 'bool',
            'packages.*.delivery_type' => 'required|in:normal,fast',
            'warehouse_cn' => 'required|exists:warehouses,id',
            'warehouse_vi' => 'required|exists:warehouses,id',
            'customer_delivery_id' => 'required|exists:customer_deliveries,id',
        ];
    }

    protected function getAttributes(): array
    {
        return [
            'packages.*.image' => 'Hình ảnh',
            'packages.*.product_name' => 'Tên sản phẩm',
            'packages.*.bill_code' => 'Mã vận đơn',
            'packages.*.packages_number' => 'Số kiện hàng',
            'packages.*.transporter' => 'Hãng vận chuyển',
            'packages.*.in_transit' => 'Chiều vận chuyển',
            'packages.*.category_id' => 'Danh mục sản phẩm',
            'packages.*.quantity' => 'Số lượng',
            'packages.*.order_cost' => 'Giá trị hàng hóa',
            'packages.*.description' => 'Nội dung',
            'packages.*.note' => 'Ghi chú',
            'packages.*.delivery_type' => 'Loại hình vận chuyển',
            'is_insurance' => 'Bảo hiểm',
            'warehouse_cn' => 'Kho Trung Quốc',
            'warehouse_vi' => 'Kho Việt Nam',
        ];
    }

    /**
     * @return JsonResponse
     */
    public function reportStatus(): JsonResponse
    {
        return resSuccessWithinData(
            $this->_service->getReportsHasQuantity((new ReportConsignment())->getTable(), ConsignmentConstant::class)
        );
    }
}
