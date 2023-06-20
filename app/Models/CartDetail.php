<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CartDetail
 * @package App\Models
 *
 * @property string $id
 * @property int $quantity
 * @property float $unit_price_cny
 * @property float $amount_cny
 *
 */
class CartDetail extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'unit_price_cny' => 'float',
        'amount_cny' => 'float',
        'quantity' => 'integer',
    ];
}
