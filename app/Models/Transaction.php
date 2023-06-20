<?php

namespace App\Models;

use App\Constants\TransactionConstant;
use App\Models\Traits\Filters\StatusFilter;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasSortByCode;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Transaction
 * @package App\Models
 *
 * @property string $id;
 * @property string $status;
 */

class Transaction extends BaseModel
{
    use HasFactory, Filterable, StatusFilter, HasSortByCode;

    protected string $_prefixRoute = 'ewallet';

    protected $dates = [
        'time'
    ];

    protected $casts = [
        'amount' => 'float',
        'balance' => 'float',
    ];

    protected $appends = ['is_positive'];

    public function scopeCreatedAt($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('created_at'), 'created_at');
    }

    public function scopeCode($query)
    {
        return $query->where('code', request()->query('code'));
    }

    public function getIsPositiveAttribute(): bool
    {
        return $this->status === TransactionConstant::STATUS_DEPOSIT || $this->status === TransactionConstant::STATUS_REFUND;
    }
}
