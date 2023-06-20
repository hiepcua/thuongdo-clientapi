<?php

namespace App\Models;

use App\Models\Traits\BankRelation;
use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationBank extends BaseModel
{
    use HasFactory, HasOrganization, BankRelation;
}
