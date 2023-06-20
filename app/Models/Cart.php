<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Cart
 * @package App\Models
 *
 * @property string $id
 * @property string $total_amount_cny
 * @property bool $is_inspection
 * @property bool $is_woodworking
 * @property bool $is_insurance
 * @property bool $is_shock_proof
 * @property bool $is_tax
 * @property string $delivery_type
 */
class Cart extends BaseModel
{
    use HasFactory;

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(CartDetail::class);
    }
}
