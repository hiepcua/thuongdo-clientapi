<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends BaseModel
{
    use HasFactory, Filterable;

    public function scopeType($query)
    {
        return $query->where('type', request()->input('type'));
    }

    public function scopeIndustry($query)
    {
        return $query->where('industry', request()->input('industry'));
    }

    public function scopeName($query)
    {
        return $query->where("name", 'like', '%' . request()->query('name') . '%');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ContactMethod::class);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(SupplierBank::class);
    }
}
