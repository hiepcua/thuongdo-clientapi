<?php


namespace App\Helpers;


use App\Models\Consignment;
use App\Models\Customer;
use App\Models\CustomerWithdrawal;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Transaction;
use App\Scopes\CustomerCurrentScope;
use App\Services\CustomerService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class RandomHelper
{
    public static function numberString(int $length): string
    {
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= mt_rand(0, 9);
        }
        return $number;
    }

    public static function phoneNumber(): string
    {
        $prefix = Arr::random(['090', '091', '092']);
        return $prefix.RandomHelper::numberString(7);
    }

    public static function customerCode(): string
    {
        return 'KH_'.date('ym').sprintf(
                "%'.05d",
                (new CustomerService())->getCustomerNumberByYearAndMonth()
            );
    }

    public static function orderCode(?int $optional = 1): string
    {
        $customer = Auth::user();
        return $customer->code.sprintf(
                "%'.04d",
                self::getCountCode(Order::class, $customer->id) + self::getCountCode(
                    Consignment::class,
                    $customer->id
                ) + $optional
            );
    }

    /**
     * @param  string  $class
     * @param  string  $customerId
     * @return string
     */
    public static function getCountCode(string $class, string $customerId): int
    {
        return (new $class)::withTrashed()->where('customer_id', $customerId)->count();
    }

    public static function getDeliveryCode(?int $optional = 1): string
    {
         $count = Delivery::query()->withoutGlobalScope(new CustomerCurrentScope())->count();
        return sprintf(
                "GH_%'.04d",
                $count + $optional
            );
    }

    public static function getWithdrawalCode(?int $optional = 1): string
    {
        $count = CustomerWithdrawal::query()->withoutGlobalScope(CustomerCurrentScope::class)->count();
        return "RT_".date('ymd').sprintf(
            "%'.04d",
            $count + $optional
        );
    }

    public static function getTransactionCode(?int $optional = 1): string
    {
        $count = Transaction::query()->withoutGlobalScope(CustomerCurrentScope::class)->count();
        return "GD_".date('ymd').sprintf(
            "%'.04d",
            $count + $optional
        );
    }
}