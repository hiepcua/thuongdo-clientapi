<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\Traits\Filterable;

class SupplierBank extends BaseModel
{
    use HasFactory, Filterable;

    protected $fillable = [
        'bank_id',
        'account_number',
        'account_holder',
        'branch',
    ];
}
