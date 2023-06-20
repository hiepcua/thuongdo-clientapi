<?php

namespace App\Models;

use App\Constants\DeliveryConstant;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\OrderMorphRelation;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasOrganization;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Delivery
 * @package App\Models
 *
 * @property string $id
 * @property string $code
 * @property string $receiver
 * @property string $phone_number
 * @property string $address
 * @property string $status
 * @property float $international_shipping_cost
 * @property float $china_shipping_cost
 * @property float $delivery_cost
 * @property float $shipping_cost
 * @property float $debt_cost
 * @property float $amount
 * @property float $insurance_cost
 * @property float $inspection_cost
 * @property float $woodworking_cost
 * @property float $shock_proof_cost
 * @property float $storage_cost
 * @property boolean $is_delivery_cost_paid
 * @property string $payment
 * @property CustomerDelivery $customerDelivery
 */
class Delivery extends BaseModel
{
    use HasFactory, HasOrganization, Filterable, StatusFilter, OrderMorphRelation;

    protected $appends = ['custom_address', 'shipping_cost', 'amount'];
    protected string $_tableNameFriendly = 'Giao hàng';

    protected $dates = [
        'date',
        'created_at'
    ];

    protected $casts = [
        'amount' => 'double',
        'shipping_cost' => 'float',
        'debt_cost' => 'float',
        'china_shipping_cost' => 'float',
        'international_shipping_cost' => 'float',
        'delivery_cost' => 'float',
        'insurance_cost' => 'float',
        'inspection_cost' => 'float',
        'shock_proof_cost' => 'float',
        'storage_cost' => 'float',
        'woodworking_cost' => 'float',
    ];

    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    public function getCustomAddressAttribute(): string
    {
        return $this->receiver.' - '.$this->phone_number.' - '.$this->address;
    }

    public function getShippingCostAttribute(): float
    {
        return $this->international_shipping_cost + $this->china_shipping_cost + $this->insurance_cost + $this->inspection_cost + $this->woodworking_cost + $this->shock_proof_cost + $this->storage_cost;
    }

    public function getAmountAttribute(): float
    {
        return $this->shipping_cost + $this->delivery_cost + $this->debt_cost;
    }

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function customerDelivery(): BelongsTo
    {
        return $this->belongsTo(CustomerDelivery::class);
    }

    public function scopeBillCode($query)
    {
        return $query->whereHas('packages', fn($q) => $q->where('bill_code', request()->query('bill_code')));
    }

    public function scopeTransporterId($query)
    {
        return $query->where('transporter_id', request()->query('transporter_id'));
    }

    public function scopePayment($query)
    {
        return $query->where('payment', request()->query('payment'));
    }

    public function scopeDate($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->input('date'), 'date');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function consignments(): HasMany
    {
        return $this->hasMany(Consignment::class);
    }
}
