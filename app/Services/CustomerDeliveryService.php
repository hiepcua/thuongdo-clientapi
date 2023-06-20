<?php


namespace App\Services;


use App\Http\Resources\Customer\CustomerDeliveryResource;
use App\Http\Resources\ListResource;
use App\Models\CustomerDelivery;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CustomerDeliveryService extends BaseService
{
    protected string $_resource = CustomerDeliveryResource::class;

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $deliveries = CustomerDelivery::query()->get();
        return resSuccessWithinData(new ListResource($deliveries, $this->_resource));
    }

    /**
     * @param  array  $data
     * @return Builder|Model|JsonResponse
     * @throws Exception
     */
    public function store(array $data)
    {
        $data['customer_id'] = Auth::user()->id;
        unset($data['is_default']);
        return parent::store($data);
    }
}