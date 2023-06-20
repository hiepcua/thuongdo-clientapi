<?php

namespace App\Http\Controllers;


use App\Services\SupplierTypeService;

class SupplierTypeController extends Controller
{
    public function __construct(SupplierTypeService $service)
    {
        $this->_service = $service;
    }
}
