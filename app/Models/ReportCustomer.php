<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportCustomer extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'balance_amount' => 'float',
        'order_amount' => 'float',
        'deposited_amount' => 'float',
        'withdrawal_amount' => 'float',
        'purchase_amount' => 'float',
        'discount_amount' => 'float',
    ];
}
