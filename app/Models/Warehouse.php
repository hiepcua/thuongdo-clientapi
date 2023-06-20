<?php

namespace App\Models;

use App\Constants\LocateConstant;
use App\Models\Traits\DistrictRelation;
use App\Models\Traits\ProvinceRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Warehouse
 * @package App\Models
 *
 * @property Province $province
 * @property District $district
 */

class Warehouse extends BaseModel
{
    use HasFactory, ProvinceRelation, DistrictRelation;

    protected $appends = [
        'custom_name'
    ];

    /**
     * @return string
     */
    public function getCustomNameAttribute(): string
    {
        return $this->country === LocateConstant::COUNTRY_VI ? (optional($this->province)->name . ' - ' . $this->address) : $this->name;
    }

}
