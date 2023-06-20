<?php

namespace App\Models;

use App\Constants\TimeConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\TimeHelper;
use App\Models\Traits\CategoryRelation;
use App\Models\Traits\CustomerDeliveryRelation;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\Filters\WarehouseFilter;
use App\Models\Traits\OrderMorphRelation;
use App\Models\Traits\OrderRelation;
use App\Models\Traits\WarehouseRelation;
use App\Scopes\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class OrderPackage
 * @package App\Models
 *
 * @property string $id
 * @property string $bill_code
 * @property string $order_id
 * @property float $china_shipping_cost
 * @property float $insurance_cost
 * @property float $inspection_cost
 * @property float $woodworking_cost
 * @property float $discount_cost
 * @property float $shock_proof_cost
 * @property float $storage_cost
 * @property float $international_shipping_cost
 * @property Order $order
 * @property Consignment $consignment
 */
class OrderPackage extends BaseModel
{
    use HasFactory, Filterable, WarehouseRelation, OrderMorphRelation, StatusFilter, WarehouseFilter, CustomerDeliveryRelation;

    protected string $_tableNameFriendly = 'Kiện';
    protected string $_colorLog = '#F24C39';
    protected string $_prefixRoute = 'package';

    protected $table = 'order_package';

    protected $appends = ['amount', 'shipping_cost', 'order_code', 'date_ordered'];

    protected $casts = [
        'delivery_cost' => 'double',
        'international_shipping_cost' => 'double',
        'china_shipping_cost' => 'double',
        'inspection_cost' => 'double',
        'insurance_cost' => 'double',
        'storage_cost' => 'double',
        'shock_proof_cost' => 'double',
        'discount_cost' => 'double',
        'woodworking_cost' => 'double',
        'amount' => 'double',
        'volume' => 'double',
        'weight' => 'double',
        'is_inspection' => 'bool',
        'is_insurance' => 'bool',
        'is_woodworking' => 'bool',
        'is_delivery' => 'bool',
    ];

    /**
     * @return float
     */
    public function getAmountAttribute(): float
    {
        return AccountingHelper::getCosts(
            $this->international_shipping_cost + $this->china_shipping_cost +
            $this->insurance_cost + $this->inspection_cost + $this->woodworking_cost + $this->shock_proof_cost + $this->storage_cost - $this->discount_cost
        );
    }

    /**
     * @return float
     */
    public function getShippingCostAttribute(): float
    {
        return AccountingHelper::getCosts(
            $this->international_shipping_cost + $this->china_shipping_cost + $this->insurance_cost + $this->inspection_cost + $this->woodworking_cost + $this->shock_proof_cost + $this->storage_cost
        );
    }

    public function getOrderCodeAttribute()
    {
        return optional($this->order)->code ?? optional($this->consignment)->code;
    }

    public function getDateOrderedAttribute(): ?string
    {
        return TimeHelper::format(
            optional($this->order)->date_ordered ?? optional($this->consignment)->created_at,
            TimeConstant::DATE_VI
        );
    }

    /**
     * @return BelongsTo
     */
    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class, 'order_id');
    }

    public function scopeBillCode($query)
    {
        return $query->where('bill_code', request()->query('bill_code'));
    }

    public function scopeOrderCode($query)
    {
        return $query->where('order_code', request()->query('order_code'));
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function scopeType($query)
    {
        if (!request()->query('type')) {
            return $query;
        }
        $isOrder = request()->query('type') === 'order';
        return $query->where('is_order', $isOrder);
    }

    public function consignmentDetail(): HasOne
    {
        return $this->hasOne(ConsignmentDetail::class);
    }

    public function orderDetail(): HasOne
    {
        return $this->hasOne(OrderDetail::class);
    }

    public function orderDetails(): BelongsToMany
    {
        return $this->belongsToMany(OrderDetail::class, 'order_detail_packages');
    }

    public function transporterRelation(): BelongsTo
    {
        return $this->belongsTo(Transporter::class, 'transporter_id', 'id');
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
