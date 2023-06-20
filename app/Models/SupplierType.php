<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\Traits\Filterable;

class SupplierType extends BaseModel
{
    use HasFactory, Filterable;
}
