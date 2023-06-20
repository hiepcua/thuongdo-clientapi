<?php


namespace App\Models\Traits;


use App\Models\CustomerDelivery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CustomerDeliveryRelation
{
    public function customerDelivery(): BelongsTo
    {
        return $this->belongsTo(CustomerDelivery::class);
    }
}