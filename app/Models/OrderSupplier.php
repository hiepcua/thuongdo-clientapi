<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class OrderSupplier
 * @package App\Models
 *
 * @property float $discount_cost
 * @property float $order_cost
 * @property float $inspection_cost
 * @property float $international_shipping_cost
 * @property float $china_shipping_cost
 * @property float $total_amount
 * @property bool $is_inspection
 */

class OrderSupplier extends BaseModel
{
    use HasFactory;

    protected $table = 'order_supplier';

    protected $appends = ['total_amount'];

    protected $casts = [
        'order_cost' => 'float',
        'order_fee' => 'float',
        'discount_cost' => 'float',
        'inspection_cost' => 'float',
        'international_shipping_cost' => 'float',
        'china_shipping_cost' => 'float',
        'total_amount' => 'float',
    ];

    public function getTotalAmountAttribute(): float
    {
        return $this->order_cost + $this->order_fee + $this->international_shipping_cost + $this->china_shipping_cost + $this->inspection_cost - $this->discount_cost;
    }
}
