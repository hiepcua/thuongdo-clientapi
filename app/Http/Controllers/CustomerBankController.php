<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Services\CustomerBankService;

class CustomerBankController extends Controller implements StoreValidationInterface, UpdateValidationInterface
{
    public function __construct(CustomerBankService $service)
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
            'account_number' => 'required|max:50',
            'bank_id' => 'required|exists:banks,id',
            'account_holder' => 'required|max:255',
            'branch' => 'required|max:255',
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

    protected function getAttributes(): array
    {
        return [
            'account_number' => 'Tài khoản',
            'bank_id' => 'Ngân hàng',
            'account_holder' => 'Tên',
            'branch' => ' Chi nhánh'
        ];
    }
}
