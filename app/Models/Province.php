<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Province
 * @package App\Models
 *
 * @property string $id
 * @property string $name
 * @property int $viettel_id
 * @property int $ghn_id
 */
class Province extends BaseModel
{
    use HasFactory;
}
