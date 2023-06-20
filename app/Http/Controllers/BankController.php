<?php

namespace App\Http\Controllers;

use App\Services\BankService;
use Illuminate\Http\JsonResponse;

class BankController extends Controller
{
    public function __construct(BankService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param string $country
     * @return JsonResponse
     */
    public function getBankByCountry(string $country): JsonResponse
    {
        return $this->_service->getBankByCountry($country);
    }
}
