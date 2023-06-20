<?php

namespace App\Models;

use App\Helpers\TimeHelper;
use App\Models\Traits\CustomerDeliveryRelation;
use App\Models\Traits\CustomerRelation;
use App\Models\Traits\Filters\StatusFilter;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasOrganization;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Consignment
 * @package App\Models
 *
 * @property string $id
 * @property string $code
 * @property string $warehouse_cn
 * @property string $warehouse_vi
 * @property string $customer_id
 * @property string $customer_delivery_id
 * @property string $organization_id
 * @property int $status
 */
class Consignment extends BaseModel
{
    use HasFactory, Filterable, CustomerDeliveryRelation, SoftDeletes, StatusFilter, CustomerRelation;

    protected string $_tableNameFriendly = 'Ký gửi';

    protected $dates = ['date_ordered'];

    public function warehouseCn(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_cn', 'id');
    }

    public function warehouseVi(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_vi', 'id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class, 'order_id', 'id');
    }

    public function scopeCode($query)
    {
        return $query->where('code', request()->input('code'));
    }

    public function scopeWarehouseId($query)
    {
        return $query->where('warehouse_vi', request()->input('warehouse_id'));
    }

    public function scopeDate($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->input('date'), 'date_ordered');
    }
}
