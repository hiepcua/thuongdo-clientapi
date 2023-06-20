<?php

namespace App\Models;

use App\Models\Traits\CategoryRelation;
use App\Models\Traits\ImageAttribute;
use App\Models\Traits\OrderPackageRelation;
use App\Models\Traits\OrderRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetail extends BaseModel
{
    use HasFactory, ImageAttribute, CategoryRelation, OrderPackageRelation, OrderRelation;

    protected $fillable = [
        'order_id',
        'name',
        'link',
        'image',
        'classification',
        'note',
        'unit_price_cny',
        'quantity',
        'amount_cny',
        'supplier_id',
        'category_id',
        'order_package_id',
        'complain_id',
        'complain_note',
        'organization_id'
    ];

    protected $casts = [
        'amount_cny' => 'float',
        'unit_price_cny' => 'float',
        'quantity' => 'integer',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(OrderDetailImage::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
