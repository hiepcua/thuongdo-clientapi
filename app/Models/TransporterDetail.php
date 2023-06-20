<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransporterDetail extends BaseModel
{
    use HasFactory;

    protected $appends = ['custom_name'];

    public function getCustomNameAttribute(): string
    {
        return $this->name.' - '.$this->phone_number;
    }
}
