<?php


namespace App\Services;


use App\Helpers\PaginateHelper;
use App\Http\Resources\Customer\CustomerBankGroupByResource;
use App\Http\Resources\Customer\CustomerBankResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\PaginateJsonResource;
use App\Models\CustomerBank;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CustomerBankService extends BaseService
{
    protected string $_resource = CustomerBankResource::class;

    /**
     * @param  array  $data
     * @return Builder|Model|JsonResponse
     * @throws Exception
     */
    public function store(array $data)
    {
        $data['customer_id'] = Auth::user()->id;
        return parent::store($data);
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = CustomerBank::query()->get()->groupBy('bank_id')->values();
        return resSuccessWithinData(new ListResource($data, CustomerBankGroupByResource::class));
    }
}