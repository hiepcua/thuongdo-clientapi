<?php


namespace App\Services;


use App\Helpers\AccountingHelper;
use App\Models\Complain;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailImage;
use App\Models\OrderSupplier;
use Illuminate\Support\Arr;

class OrderDetailService extends BaseService
{
    public function insertFromOrder(Order $order, array $orderDetail)
    {
        foreach ($orderDetail as $key => $item) {
            $array = array_merge((new OrderDetail())->getFillable(), ['supplier']);
            $item['link'] = $item['url'];
            removeKeyNotExistsModel($item, $array);
            $item['organization_id'] = getOrganization();
            $item['order_id'] = $order->id;
            $item['amount_cny'] = $item['unit_price_cny'] * $item['quantity'];
            $item['supplier_id'] = (new SupplierService())->firstOrCreateSupplierByName($item['supplier'])->id;
            unset($item['supplier']);
            $orderDetail[$key] = $item;
        }
        // Thêm Order có những supplier và dịch nào của supplier
        $this->insertOrderSupplier($order, $orderDetail);
        OrderDetail::query()->insert($orderDetail);
    }

    /**
     * @param  array  $params
     * @param  Complain  $complain
     */
    public function insertComplainImage(array $params, Complain $complain): void
    {
        $detailImages = [];
        $details = [];
        $products = $params['products'];
        foreach ($products as $product) {
            $details[$product['order_detail_id']] = [
                'id' => getUuid(),
                'note' => $product['complain_note'] ?? null,
                'complain_id' => $complain->id,
                'order_package_id' => $params['order_package_id'],
                'created_at' => now()
            ];
            if (isset($product['images'])) {
                
                foreach ($product['images'] as $image) {
                    $detailImages[] = [
                        'order_detail_id' => $product['order_detail_id'],
                        'complain_id' => $complain->id,
                        'image' => $image,
                        'id' => getUuid(),
                        'created_at' => now()
                    ];
                }
            }
        }
        $complain->orderDetails()->sync($details);
        OrderDetailImage::query()->where(['order_detail_id' => $product['order_detail_id'], 'complain_id' => $complain->id])->delete();
        OrderDetailImage::query()->insert($detailImages);
    }

    /**
     * @param  Order  $order
     * @param  array  $orderDetail
     */
    private function insertOrderSupplier(Order $order, array $orderDetail): void
    {
        $suppliers = collect($orderDetail)->groupBy('supplier_id');
        $orderSuppliers = [];
        $countSuppliers = count($suppliers);
        $accountService = new AccountingService();
        foreach ($suppliers as $supplier => $value) {
            $orderCost = AccountingHelper::getCosts(
                $value->sum('amount_cny') * $order->exchange_rate
            );
            $productsCount = array_sum(Arr::pluck($value, 'quantity'));
            $orderSuppliers[] = [
                'id' => getUuid(),
                'order_id' => $order->id,
                'supplier_id' => $supplier,
                'order_cost' => $orderCost,
                'is_shock_proof' => $order->is_shock_proof,
                'order_fee' => $orderFee = $accountService->getOrderFee($orderCost),
                'is_inspection' => $isInspection = $order->is_inspection,
                'is_woodworking' => $order->is_woodworking,
                'delivery_type' => $order->delivery_type,
                'inspection_cost' => (bool)$isInspection ? $accountService->getInspectionCost($productsCount) : 0,
                'international_shipping_cost' => $internationCost = AccountingHelper::getCosts(
                    $order->international_shipping_cost / $countSuppliers
                ),
                'discount_cost' => $accountService->getDiscountCost($orderFee + $internationCost),
                'created_at' => now()
            ];
        }
        OrderSupplier::query()->insert($orderSuppliers);
    }
}