<?php


namespace App\Services;


use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;

class BankService extends BaseService
{
    protected string $_resource = BankResource::class;

    /**
     * @param  string  $country
     * @return JsonResponse
     */
    public function getBankByCountry(string $country): JsonResponse
    {
        $data = Bank::query()->where('country', $country)->get();
        return resSuccessWithinData(new $this->_listResource($data, $this->_resource));
    }
}
