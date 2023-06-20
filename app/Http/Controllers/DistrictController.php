<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListResource;
use App\Http\Resources\LocateResource;
use App\Models\District;
use App\Models\Ward;
use App\Services\DistrictService;
use Illuminate\Http\JsonResponse;

class DistrictController extends Controller
{
    public function __construct(DistrictService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  string  $provinceId
     * @return JsonResponse
     */
    public function listByProvinceId(string $provinceId): JsonResponse
    {
        return resSuccessWithinData(
            new ListResource(District::query()->where('province_id', $provinceId)->get(), LocateResource::class)
        );
    }

    /**
     * @param  string  $districtId
     * @return JsonResponse
     */
    public function listByDistrictId(string $districtId): JsonResponse
    {
        return resSuccessWithinData(
            new ListResource(Ward::query()->where('district_id', $districtId)->get(), LocateResource::class)
        );
    }
}
