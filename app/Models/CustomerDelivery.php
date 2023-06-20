<?php

namespace App\Models;

use App\Models\Traits\DistrictRelation;
use App\Models\Traits\ProvinceRelation;
use App\Models\Traits\WardRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerDelivery
 * @package App\Models
 *
 * @property string $id
 * @property string $address
 * @property string $custom_name
 * @property float $delivery_cost
 * @property Province $province
 * @property District $district
 * @property Ward $ward
 */

class CustomerDelivery extends BaseModel
{
    use HasFactory, ProvinceRelation, DistrictRelation, WardRelation;

    protected $table = 'customer_deliveries';

    protected $appends = [
        'custom_name',
        'address_only',
    ];

    /**
     * @return string
     */
    public function getCustomNameAttribute(): string
    {
        return ucwords(
            mb_strtolower(
                $this->receiver.' - '.$this->phone_number.' - '.$this->address.' - '.optional(
                    $this->ward
                )->name.' - '.optional($this->district)->name.' - '.optional($this->province)->name
            )
        );
    }

    public function getAddressOnlyAttribute(): string
    {
        return ucwords(
            mb_strtolower(
                $this->address.' - '.optional(
                    $this->ward
                )->name.' - '.optional($this->district)->name.' - '.optional($this->province)->name
            )
        );
    }
}
