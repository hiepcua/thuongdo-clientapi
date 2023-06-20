<?php


namespace App\Services;


use App\Models\ContactMethod;
use App\Models\Supplier;
use App\Http\Resources\Supplier\SupplierResource;
use App\Scopes\OrganizationScope;
use http\Env\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Helpers\PaginateHelper;
use App\Http\Resources\ListResource;
use function Composer\Autoload\includeFile;

class SupplierService extends BaseService
{
    /**
     * @param string $name
     * @return Builder|Model|object|null
     */
    protected string $_resource = SupplierResource::class;

    public function getListSup($request): JsonResponse
    {
        $sup = Supplier::query()->orderBy('order_amount', $request->sort ?? 'ASC')->paginate();
        return resSuccessWithinData(new $this->_paginateResource($sup, $this->_resource));
    }

    public function firstOrCreateSupplierByName(string $name, ?string $code = null)
    {
        $supplier = Supplier::query()->withoutGlobalScope(OrganizationScope::class)->where(['name' => $name, 'customer_id' => getCurrentUserId()])->first();
        if($supplier) return $supplier;
        return Supplier::query()->withoutGlobalScope(OrganizationScope::class)->create(
            [
                'name' => $name,
                'code' => $code ?? 'CC_' . Str::random(12),
                'organization_id' => getOrganization(),
                'customer_id' => getCurrentUserId()
            ]
        );
    }

    public function store(array $data): Supplier
    {
        return DB::transaction(
            function () use ($data) {
                $data['customer_id'] = Auth::user()->id;
                if (!isset($data['code'])) $data['code'] = 'CC_' . Str::random(12);
                $supplier = parent::store($data);
                if (isset($data['type_contact']) && isset($data['type_bank'])) {
                    (new ContactMethodService)->insertFromSupplier($supplier, $data['type_contact']);
                    (new SupplierBankService)->insertFromSupplier($supplier, $data['type_bank']);
                }
                return ($supplier);
            }
        );
    }
//

}
