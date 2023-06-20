<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends BaseModel
{
    use HasFactory;

    protected $appends = [
        'type'
    ];

    public function getTypeAttribute(): string
    {
        if (strpos($this->mime_type, 'video') !== false) {
            return 'video';
        }
        if (strpos($this->mime_type, 'image') !== false) {
            return 'image';
        }
        return 'file';
    }
}
