<?php

namespace App\Http\Controllers;

use App\Http\Resources\Warehouse\WarehouseListResource;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    /**
     * WarehouseController constructor.
     * @param  WarehouseService  $warehouseService
     */
    public function __construct(WarehouseService $warehouseService)
    {
        $this->_service = $warehouseService;
    }

    /**
     * @param  string  $country
     * @return JsonResponse
     */
    public function getListByCountry(string $country): JsonResponse
    {
        return resSuccessWithinData(
            new WarehouseListResource(
                Warehouse::query()->where('country', $country)->get()
            )
        );
    }
}
