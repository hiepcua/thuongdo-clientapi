<?php

namespace App\Http\Controllers;


use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(SupplierService $service)
    {
        $this->_service = $service;
    }
    public function getList(Request $request)
    {
        return $this->_service->getListSup($request);
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'name' => 'required|max:255',
        ];
    }
}
