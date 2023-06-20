<?php

namespace App\Models;

use App\Constants\TimeConstant;
use App\Models\Traits\AvatarAttribute;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\Filters\WarehouseFilter;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laravel\Sanctum\HasApiTokens;


/**
 * Class Customer
 * @package App\Models
 *
 * @property string $id
 * @property string $warehouse_id
 * @property string $bill_code
 * @property string $verify_code
 * @property string $organization_id
 * @property int $level
 * // * @method Builder search
 */
class Customer extends Authenticate
{
    use HasApiTokens, HasFactory, Filterable, SoftDeletes, HasOrganization, AvatarAttribute, StatusFilter, WarehouseFilter;

    protected $casts = [
        'created_at' => 'date:'.TimeConstant::DATETIME,
        'updated_at' => 'date:'.TimeConstant::DATETIME
    ];

    protected string $_tableNameFriendly = 'Khách hàng';

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function report(): HasOne
    {
        return $this->HasOne(ReportCustomer::class);
    }

    public function scopeName($query)
    {
        return $query->where('name', 'like', '%'.request()->query('code').'%');
    }

    public function scopeCustomerCode($query)
    {
        return $query->where('code', request()->query('customer_code'));
    }

    public function staffOrder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_order_id', 'id');
    }

    public function staffCare(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_care_id', 'id');
    }
}
