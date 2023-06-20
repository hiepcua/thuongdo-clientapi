<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends BaseModel
{
    use HasFactory;

    protected $dates = ['created_at'];

}
