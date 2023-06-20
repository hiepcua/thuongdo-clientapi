<?php


namespace App\Services;

use App\Models\ContactMethod;
use App\Models\Supplier;
use App\Http\Resources\Supplier\SupplierTypeResource;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SupplierTypeService extends BaseService
{
    /**
     * @param string $name
     * @return Builder|Model|object|null
     */
    protected string $_resource = SupplierTypeResource::class;

}
