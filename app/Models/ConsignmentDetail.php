<?php

namespace App\Models;

use App\Models\Traits\CategoryRelation;
use App\Models\Traits\ImageAttribute;
use App\Models\Traits\OrderPackageRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsignmentDetail extends BaseModel
{
    use HasFactory, OrderPackageRelation, CategoryRelation, ImageAttribute;

    protected $fillable = [
        'consignment_id',
        'order_package_id',
        'quantity',
        'order_cost',
        'category_id',
        'image',
        'packages_number',
        'name'
    ];
}
