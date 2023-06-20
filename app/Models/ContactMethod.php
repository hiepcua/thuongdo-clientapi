<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\Traits\Filterable;

class ContactMethod extends BaseModel
{
    use HasFactory,Filterable;

    protected $fillable = [
        'name',
        'supplier_type_id',
        'position',
        'details',
    ];
}
