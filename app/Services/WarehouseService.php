<?php


namespace App\Services;


use App\Constants\LocateConstant;
use App\Models\Warehouse;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WarehouseService extends BaseService
{
    /**
     * @param  string  $country
     * @return Builder|Model|object|null
     */
    public function getWarehouseRandomByCountry(string $country)
    {
        return Warehouse::query()->withoutGlobalScope(OrganizationScope::class)->where(
            ['country' => $country]
        )->inRandomOrder()->firstOrFail();
    }

    /**
     * @param  string  $id
     * @return string
     */
    public function getProvinceById(string $id): string
    {
        $province = array_search(Warehouse::query()->find($id)->province->name, LocateConstant::HANOI_HCM_HP);
        $province = $province === false ? LocateConstant::HANOI : $province;
        return $province;
    }
}