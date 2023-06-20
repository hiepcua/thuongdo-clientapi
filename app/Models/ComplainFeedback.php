<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ComplainFeedback extends BaseModel
{
    use HasFactory;

    protected $dates = [
        'created_at'
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(ComplainFeedbackAttachment::class);
    }

    public function cause(): MorphTo
    {
        return $this->morphTo();
    }
}
