<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Helpers\ValidationHelper;
use App\Http\Requests\CustomerChangePasswordRequest;
use App\Http\Resources\ProfileResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class CustomerController
 * @package App\Http\Controllers
 */
class CustomerController extends Controller implements StoreValidationInterface, UpdateValidationInterface
{
    public function __construct(CustomerService $service)
    {
        $this->_service = $service;
    }

    public function storeMessage(): array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'name' => 'required|max:255',
            'phone_number' => 'required|max:13',
            'email' => 'required|max:255|email|unique:customers',
            'password' => 'required|max:255',
            'bod' => 'required|date_format:Y-m-d',
            'warehouse_id' => 'required|max:255|exists:warehouses,id',
            'address' => 'required|string|max:255',
            'staff_care_id' => 'exists:users,id',
            'staff_counselor_id' => 'exists:users,id',
            'status' => 'required|in:0,1',
            'facebook_url' => 'url|max:255',
            'skype_url' => 'url|max:255',
            'feelings' => 'string|max:500',
            'district_id' => 'required|uuid|exists:districts,id',
            'province_id' => 'required|uuid|exists:districts,id',
            'gender' => 'required|in:male,female,undetermined',
            'delivery_type' => 'in:fast,normal|string',
            'avatar' => 'string|max:255',
            'customer_delivery_id' => 'exists:customer_deliveries,id',
            'service' => 'in:1,0'
        ];
    }

    public function updateMessage(): array
    {
        return [];
    }

    public function updateRequest(string $id): array
    {
        $data = $this->storeMessage();
        ValidationHelper::prepareUpdateAction($data, $id);
        return $data;
    }

    /**
     * @return JsonResponse
     * @throws \Throwable
     */
    public function profile(): JsonResponse
    {
        $customer = Auth::user();
        $id = $customer->id;
        $rules = $this->storeRequest();
        ValidationHelper::prepareUpdateAction($rules, $id);
        throw_if(
            $errors = ValidationHelper::validation(request()->all(), $rules),
            CustomValidationException::class,
            $errors
        );
        $customer->update(request()->except('organization_id'));
        if (request()->has('customer_delivery_id')) {
            $this->_service->setDeliveryDefault($id, request()->input('customer_delivery_id'));
        }
        return resSuccessWithinData($customer);
    }

    /**
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return resSuccessWithinData(
            new ProfileResource(Auth::user())
        );
    }

    /**
     * @param  CustomerChangePasswordRequest  $request
     * @return JsonResponse
     */
    public function changePassword(CustomerChangePasswordRequest $request): JsonResponse
    {
        [$passwordOld, $password] = array_values($request->all());
        $user = Auth::user();
        if (!Hash::check($passwordOld, $user->password)) {
            return resError(trans('passwords.invalid'));
        }
        $user->password = Hash::make($password);
        $user->save();
        return resSuccess(trans('passwords.changed'));
    }
}
