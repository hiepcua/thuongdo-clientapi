<?php

namespace App\Models;

use App\Scopes\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends BaseModel
{
    use HasFactory, Filterable;

    protected $dates = [
        'created_at'
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }

    public function causer(): MorphTo
    {
        return $this->morphTo('causer');
    }

    public function scopeLogName($query)
    {
        return $query->where('log_name', request()->query('log_name'));
    }
}
