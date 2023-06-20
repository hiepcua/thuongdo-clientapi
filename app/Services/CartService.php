<?php


namespace App\Services;


use App\Helpers\AccountingHelper;
use App\Helpers\PaginateHelper;
use App\Helpers\RandomHelper;
use App\Http\Resources\CartResource;
use App\Http\Resources\ListResource;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderSupplier;
use App\Scopes\OrganizationScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService extends BaseService
{
    protected string $_resource = CartResource::class;

    public function index(): JsonResponse
    {
        $cart = Cart::query()->limit(
            PaginateHelper::getLimit()
        )->get();
        return resSuccessWithinData((new ListResource($cart, $this->_resource)));
    }

    public function createWithoutScope(array $array)
    {
        return Cart::query()->withoutGlobalScope(OrganizationScope::class)->firstOrCreate($array);
    }

    public function orderUp(array $params): JsonResponse
    {
        return DB::transaction(
            function () use ($params) {
                if (!isset($params['carts']) || !isset($params['customer_delivery_id']) || !isset($params['warehouse_id'])) {
                    return resError();
                }
                /** @var Order $order */
                $order = Order::query()->create($this->getOrder($params));
                $carts = $params['carts'];
                $link = null;
                $orderCost = 0;
                $isInspection = false;
                $isWoodworking = false;
                $isShockProof = false;
                foreach ($carts as $item) {
                    $cart = Cart::query()->find($item['id']);
                    $link = str_replace(['detail.', 'item.'], '', parse_url($cart->products->first()->link, PHP_URL_HOST));
                    $orderCost += $this->storeOrderDetail(
                        $cart,
                        $item,
                        $order->id,
                        $cart->supplier_id
                    );
                    if($item['is_inspection']) $isInspection = true;
                    if($item['is_woodworking']) $isWoodworking = true;
                    if($item['is_shock_proof']) $isShockProof = true;
                    if ($cart->products->count() == count($item['products'])) {
                        $cart->delete();
                    }
                    CartDetail::query()->whereIn('id', ($item['products']))->delete();

                    (new NoteService())->storeOrderNote(
                        ['id' => $order->id, 'content' => $item['note'], 'supplier_id' => $cart->supplier_id, 'is_public' => 1]
                    );

                }
                $order->is_woodworking = $isWoodworking;
                $order->is_inspection = $isInspection;
                $order->is_shock_proof = $isShockProof;
                $order->ecommerce = $link;
                $order->order_fee = $orderFee = (new AccountingService())->getOrderFee($orderCost, $percent);
                $order->order_percent = $percent;
                $order->discount_cost = (new AccountingService())->getDiscountCost($orderFee);
                $order->inspection_cost = (new OrderService())->updateOrderSupplier($order);
                $order->save();

                return resSuccessWithinData($order->only('id', 'code'));
            }
        );
    }

    private function getOrder(array $item): array
    {
        $exchangeRate = (float)(new ConfigService())->getExchangeCost();
        $orderCost = 0;
        $inspectionCost = 0;
        foreach ($item['carts'] as $value) {
            $orderCost += AccountingHelper::getCosts(
                CartDetail::query()->find($value['products'])->sum('amount_cny') * $exchangeRate
            );
            $cart = Cart::query()->find($value['id']);
            $inspectionCost += $cart['is_inspection'] ? (new AccountingService())->getInspectionCost(
                count($item['products'])
            ) : 0;
        }
        /** @var Customer $customer */
        $customer = Auth::user();
        $data = [
            'customer_id' => $customer->id,
            'customer_delivery_id' => $item['customer_delivery_id'],
            'warehouse_id' => $item['warehouse_id'],
            'code' => RandomHelper::orderCode(),
            'order_cost' => $orderCost,
            'order_fee' => $orderFee = (new AccountingService())->getOrderFee($orderCost, $orderPercent),
            'inspection_cost' => $inspectionCost,
            'exchange_rate' => $exchangeRate,
            'date_ordered' => now(),
            'order_percent' => $orderPercent,
            'organization_id' => getOrganization()
        ];
        (new OrderService())->assignStaffToOrder($data, $customer);
        return $data;
    }

    private function storeOrderDetail(Cart $cart, array $item, string $orderId, string $supplierId): float
    {
        $orderCost = 0;
        $products = $item['products'];
        foreach ($products as $product) {
            $detail = CartDetail::query()->findOrFail($product['id'])->toArray();
            $detail['order_id'] = $orderId;
            $detail['supplier_id'] = $supplierId;
            $detail['note'] = $product['modifies']['note'];
            $detail['quantity'] = $product['modifies']['quantity'];
            $detail['organization_id'] = getOrganization();

            $detail = OrderDetail::query()->create($detail);

            (new NoteService())->storeOrderDetailNote(
                ['id' => $detail->id, 'content' => $detail->note, 'supplier_id' => $supplierId, 'order_id' => $orderId]
            );
            $orderCost += AccountingHelper::getCosts($detail->amount_cny * (new ConfigService())->getExchangeCost());
        }

        // Thêm Order có những supplier và dịch nào của supplier
        OrderSupplier::query()->create(
            [
                'order_id' => $orderId,
                'supplier_id' => $supplierId,
                'order_cost' => $orderCost,
                'order_fee' => (new AccountingService())->getOrderFee($orderCost),
                'is_inspection' => $isInspection = (bool)$item['is_inspection'],
                'is_woodworking' => (bool)($item['is_woodworking'] ?? false),
                'is_insurance' => (bool)($item['is_insurance'] ?? false),
                'is_shock_proof' => (bool)($item['is_shock_proof'] ?? false),
                'delivery_type' => $item['delivery_type'],
                'inspection_cost' => $isInspection ? (new AccountingService())->getInspectionCost(
                    $orderCost
                ) : 0
            ]
        );
        return $orderCost;
    }
}
