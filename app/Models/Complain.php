<?php

namespace App\Models;

use App\Constants\ColorConstant;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\ImageAttribute;
use App\Models\Traits\OrderPackageRelation;
use App\Models\Traits\StaffServicesRelation;
use App\Scopes\Traits\Filterable;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Complain
 * @package App\Models
 *
 * @property string $id
 * @property string $order_id
 * @property string $status
 */
class Complain extends BaseModel
{
    use HasFactory, ImageAttribute, Filterable, StatusFilter, OrderPackageRelation, StaffServicesRelation;

    protected string $_tableNameFriendly = 'Khiếu nại';

    protected string $_colorLog = ColorConstant::CARROT_ORANGE;

    protected $dates = [
        'created_at'
    ];

    public function feedbacks(): HasMany
    {
        return $this->hasMany(ComplainFeedback::class);
    }

    public function complainType(): BelongsTo
    {
        return $this->belongsTo(ComplainType::class);
    }

    public function orderDetails(): BelongsToMany
    {
        return $this->belongsToMany(OrderDetail::class, 'complain_details');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ComplainImage::class);
    }

    public function scopeComplainTypeId($query)
    {
        return $query->where('complain_type_id', request()->query('complain_type_id'));
    }

    public function scopeSolutionId($query)
    {
        return $query->where('solution_id', request()->query('solution_id'));
    }

    public function scopeCareStaffId($query)
    {
        return $query->where('staff_care_id', request()->query('care_staff_id'));
    }

    public function scopeComplainStaffId($query)
    {
        return $query->where('complain_staff_id', request()->query('complain_staff_id'));
    }

    public function scopeOrderCode($query)
    {
        return $query->whereHas('order', function($q) {
            return $q->where('code', request()->query('order_code'));
        });
    }

    public function scopeReceiverName($query)
    {
        return $query->whereHas('order.delivery', function($q) {
            return $q->where('receiver', 'like', '%' . request()->query('receiver_name') . '%');
        });
    }

    public function scopeReceiverPhone($query)
    {
        return $query->whereHas('order.delivery', function($q) {
            return $q->where('phone_number', 'like', '%' . request()->query('receiver_phone') . '%');
        });
    }

    public function scopeCreatedAt($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->input('created_at'), 'created_at');
    }

}
