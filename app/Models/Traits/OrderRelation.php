<?php


namespace App\Models\Traits;


use App\Models\Consignment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait OrderRelation
{
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class, 'order_id', 'id');
    }
}