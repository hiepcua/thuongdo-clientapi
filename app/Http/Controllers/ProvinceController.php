<?php

namespace App\Http\Controllers;

use App\Services\ProvinceService;

class ProvinceController extends Controller
{
    public function __construct(ProvinceService $service)
    {
        $this->_service = $service;
    }
}
