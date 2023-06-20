<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class District
 * @package App\Models
 *
 * @property string $id
 * @property string $name
 * @property int $viettel_id
 * @property int $ghn_id
 */
class District extends BaseModel
{
    use HasFactory;
}
