<?php

namespace App\Models;

use App\Models\Traits\BankRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends BaseModel
{
    use HasFactory, SoftDeletes, BankRelation;
}
