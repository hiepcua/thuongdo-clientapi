<?php

namespace App\Http\Controllers;

use App\Services\SupplierBankService;
use Illuminate\Http\Request;

class SupplierBankController extends Controller
{
    public function __construct(SupplierBankService $service)
    {
        $this->_service = $service;
    }
}
