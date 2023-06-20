<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailNote;

class OrderDetailNoteObserve
{
    /**
     * Handle the Note "created" event.
     *
     * @param  \App\Models\OrderDetailNote  $note
     * @return void
     */
    public function created(OrderDetailNote $note)
    {
        Order::query()->find($note->order_id)->increment('note_number');
        OrderDetail::query()->find($note->order_detail_id)->increment('note_number');
    }
}
