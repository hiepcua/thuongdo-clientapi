<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CustomerWithdrawal
 * @package App\Models
 *
 * @property string $account_number
 * @property string $account_holder
 * @property string $branch
 * @property string $code
 * @property float $amount
 * @property string $bank
 */
class CustomerWithdrawal extends BaseModel
{
    use HasFactory;

    protected $table = 'customer_withdrawal';

    protected string $_tableNameFriendly = 'Rút tiền';

    protected $appends = ['info'];

    protected $dates = [
        'created_at' => 'datetime'
    ];

    protected $casts = [
        'amount' => 'float'
    ];

    public function getInfoAttribute(): string
    {
        return $this->account_number.','.$this->account_holder.','. $this->bank;
    }
}
