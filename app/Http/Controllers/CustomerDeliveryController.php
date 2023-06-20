<?php

namespace App\Http\Controllers;

use App\Constants\PhoneNumberConstant;
use App\Helpers\StringHelper;
use App\Helpers\ValidationHelper;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Services\CustomerDeliveryService;

class CustomerDeliveryController extends Controller implements StoreValidationInterface, UpdateValidationInterface
{
    public function __construct(CustomerDeliveryService $service)
    {
        $this->_service = $service;
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'receiver' => 'required|max:255|string',
            'phone_number' => 'required|max:10|starts_with:'.StringHelper::convertArrayToString(
                    PhoneNumberConstant::PREFIX
                ),
            'province_id' => 'required|uuid|exists:provinces,id',
            'district_id' => 'required|uuid|exists:districts,id',
            'ward_id' => 'required|uuid|exists:wards,id',
            'address' => 'required|string|max:255'
        ];
    }

    public function updateMessage(): array
    {
        return [];
    }

    public function updateRequest(string $id): array
    {
        $data = $this->storeRequest();
        ValidationHelper::prepareUpdateAction($data, $id);
        return $data;
    }
}
