<?php


namespace App\Models\Traits;


use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CategoryRelation
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}