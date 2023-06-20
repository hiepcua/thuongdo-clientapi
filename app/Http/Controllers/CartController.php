<?php

namespace App\Http\Controllers;

use App\Helpers\AccountingHelper;
use App\Http\Requests\CartDetailUpdateRequest;
use App\Http\Requests\CartOrderUpRequest;
use App\Http\Requests\Extension\ExtStoreRequest;
use App\Http\Resources\CartResource;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Customer;
use App\Services\CartService;
use App\Services\CustomerService;
use App\Services\ExtensionService;
use App\Services\Service;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller implements UpdateValidationInterface
{
    private Service $_extensionService;

    public function __construct(CartService $service, ExtensionService $extensionService)
    {
        $this->_service = $service;
        $this->_extensionService = $extensionService;
    }

    /**
     * Cập nhật số lượng + link sản phẩm
     * @param  CartDetailUpdateRequest  $request
     * @param  CartDetail  $cartDetail
     * @return JsonResponse
     */
    public function updateCartDetail(CartDetailUpdateRequest $request, CartDetail $cartDetail): JsonResponse
    {
        $cartDetail->update($request->all());
        return resSuccess();
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $cartDetail = CartDetail::query()->findOrFail($id);
        $cartId = optional($cartDetail)->cart_id;
        if ($cartId && !CartDetail::query()->where('cart_id', $cartId)->exists()) {
            parent::destroy($cartId);
        }
        CartDetail::query()->find($id)->delete();
        return resSuccess();
    }

    /**
     * @return array
     */
    public function updateMessage(): array
    {
        return [];
    }

    /**
     * @param  string  $id
     * @return string[]
     */
    public function updateRequest(string $id): array
    {
        return [
            'delivery_type' => 'in:fast,normal|string',
            'is_inspection' => 'boolean',
            'is_woodworking' => 'boolean',
            'is_shock_proof' => 'boolean',
            'note_for_organization' => 'string|max:500',
            'private_note' => 'string|max:500',
            'customer_delivery_id' => 'uuid|exists:customer_deliveries,id',
            'warehouse_id' => 'uuid|exists:warehouses,id',
        ];
    }

    /**
     * @return JsonResponse
     * @throws \Throwable
     */
    public function extGetList(): JsonResponse
    {
        return $this->_service->index();
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function extDestroySupplier(string $id): JsonResponse
    {
        return $this->_extensionService->destroy($id);
    }

    /**
     * @param  ExtStoreRequest  $request
     * @return JsonResponse
     */
    public function extStore(ExtStoreRequest $request): JsonResponse
    {
        $params = $request->all();
        /** @var Customer $customer */
        $customer = (new CustomerService())->getCustomerById(request()->input('customer_id'));
        $cart['customer_id'] = Auth::user()->id;
        $cart['organization_id'] = $customer->organization_id;
        $cart['supplier_id'] = $supplier = optional(
            (new SupplierService())->firstOrCreateSupplierByName($request->input('supplier'), $request->input('code'))
        )->id;

        /** @var Cart $cartRecord */
        $cartRecord = (new CartService())->createWithoutScope($cart);
        $cartRecord->total_amount_cny += $cart['amount_cny'] = AccountingHelper::getCosts(
            $params['quantity'] * $params['unit_price_cny']
        );
        $cartRecord->save();

        $cart['cart_id'] = $cartRecord->id;

        $params += $cart;
        CartDetail::query()->create($params);
        return resSuccessWithinData(new CartResource($cartRecord));
    }

    public function orderUp(CartOrderUpRequest $request): JsonResponse
    {
        return $this->_service->orderUp($request->all());
    }
}
