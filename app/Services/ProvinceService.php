<?php


namespace App\Services;


use App\Http\Resources\ListResource;
use App\Http\Resources\LocateResource;
use App\Models\Province;
use Illuminate\Http\JsonResponse;

class ProvinceService extends BaseService
{
    protected string $_resource = LocateResource::class;

    public function index(): JsonResponse
    {
        return resSuccessWithinData(
            new ListResource(Province::query()->orderBy('viettel_id')->get(), $this->_resource)
        );
    }
}