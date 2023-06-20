<?php

namespace App\Models;

use App\Constants\TimeConstant;
use App\Helpers\TimeHelper;
use App\Models\Traits\CustomerRelation;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\Filters\WarehouseFilter;
use App\Models\Traits\WarehouseRelation;
use App\Scopes\Traits\Filterable;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Order
 * @package App\Models
 *
 * @property string $id
 * @property string $code
 * @property string $ecommerce
 * @property float $weight
 * @property float $volume
 * @property float $order_cost
 * @property float $exchange_rate
 * @property float $order_fee
 * @property float $total_amount
 * @property float $deposit_cost
 * @property float $debt_cost
 * @property float $woodworking_cost
 * @property float $discount_cost
 * @property float $inspection_cost
 * @property float $china_shipping_cost
 * @property float $international_shipping_cost
 * @property float $order_percent
 * @property float $deposit_percent
 * @property Carbon $date_purchased
 * @property Carbon $date_done
 * @property Carbon $date_ordered
 * @property int $status
 */
class Order extends BaseModel
{
    use HasFactory, Filterable, StatusFilter, WarehouseRelation, WarehouseFilter, SoftDeletes, CustomerRelation;

    public string $_colorLog = '#00BFC4';

    public string $_tableNameFriendly = 'Đơn hàng';

    protected $appends = [
        'total_amount',
        'debt_cost'
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'order_cost' => 'float',
        'total_amount' => 'float',
        'inspection_cost' => 'float',
        'deposit_amount' => 'float',
        'woodworking_cost' => 'float',
        'discount_cost' => 'float',
        'china_shipping_cost' => 'float',
        'international_shipping_cost' => 'float',
        'order_fee' => 'float',
        'delivery_cost' => 'float'
    ];

    protected $dates = ['date_ordered', 'date_purchased', 'date_done'];

    public function products(): HasMany
    {
        return $this->hasMany(OrderDetail::class)->select('*', 'link as url');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(CustomerDelivery::class, 'customer_delivery_id', 'id');
    }

    public function scopeDateOrdered($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->input('date_ordered'), 'date_ordered');
    }

    public function scopeOrderCode($query)
    {
        return $query->where('code', request()->query('order_code'));
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->order_cost + $this->order_fee + $this->woodworking_cost - $this->discount_cost + $this->inspection_cost + $this->china_shipping_cost + $this->international_shipping_cost;
    }

    public function getDebtCostAttribute(): float
    {
        return $this->total_amount - $this->deposit_cost;
    }
}
