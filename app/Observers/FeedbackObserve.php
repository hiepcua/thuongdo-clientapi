<?php

namespace App\Observers;

use App\Models\Complain;
use App\Models\ComplainFeedback;

class FeedbackObserve
{
    /**
     * Handle the ComplainFeedback "created" event.
     *
     * @param  \App\Models\ComplainFeedback  $complainFeedback
     * @return void
     */
    public function created(ComplainFeedback $complainFeedback)
    {
        Complain::query()->find($complainFeedback->complain_id)->increment('comment_number');
    }

    /**
     * Handle the ComplainFeedback "updated" event.
     *
     * @param  \App\Models\ComplainFeedback  $complainFeedback
     * @return void
     */
    public function updated(ComplainFeedback $complainFeedback)
    {
        //
    }

    /**
     * Handle the ComplainFeedback "deleted" event.
     *
     * @param  \App\Models\ComplainFeedback  $complainFeedback
     * @return void
     */
    public function deleted(ComplainFeedback $complainFeedback)
    {
        //
    }

    /**
     * Handle the ComplainFeedback "restored" event.
     *
     * @param  \App\Models\ComplainFeedback  $complainFeedback
     * @return void
     */
    public function restored(ComplainFeedback $complainFeedback)
    {
        //
    }

    /**
     * Handle the ComplainFeedback "force deleted" event.
     *
     * @param  \App\Models\ComplainFeedback  $complainFeedback
     * @return void
     */
    public function forceDeleted(ComplainFeedback $complainFeedback)
    {
        //
    }
}
