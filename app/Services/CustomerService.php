<?php


namespace App\Services;

use App\Helpers\RandomHelper;
use App\Http\Resources\Customer\CustomerListResource;
use App\Http\Resources\Customer\CustomerPaginateResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Jobs\SetVerifyCodeNullAfterTwoMinutes;
use App\Models\Customer;
use App\Models\CustomerDelivery;
use App\Models\ReportCustomer;
use App\Models\User;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerService extends BaseService
{
    protected string $_paginateResource = CustomerPaginateResource::class;
    protected string $_listResource = CustomerListResource::class;
    protected string $_resource = CustomerResource::class;

    /**
     *  Lấy số lượng khách hàng theo tháng đã đăng ký
     * @return int
     */
    public function getCustomerNumberByYearAndMonth(): int
    {
        $code = optional(Customer::query()->whereMonth('created_at', date('m'))->whereYear(
            'created_at',
            date('Y')
        )->orderByDesc('code')->first())->code;
        return (int) ($code ? substr(filter_var($code, FILTER_SANITIZE_NUMBER_INT), 6) : 1);
    }

    public function updateCode(Customer $customer)
    {
        if ($customer->code) {
            return;
        }
        $customer->code = RandomHelper::customerCode();
        $customer->save();
    }

    public function getCustomerByEmail(string $email)
    {
        return Customer::query()->withoutGlobalScope(OrganizationScope::class)->where('email', $email)->select(
            'id',
            'name',
            'email',
            'password',
            'organization_id',
            'status'
        )->firstOrFail();
    }

    /**
     * Thay đổi mật khẩu
     * @param  string  $email
     * @param  string  $verifyCode
     * @param  string  $password
     * @return JsonResponse
     */
    public function changePassword(string $email, string $verifyCode, string $password): JsonResponse
    {
        /** @var User $customer */
        $customer = Customer::query()->withoutGlobalScope(OrganizationScope::class)->where(
            ['email' => $email, 'verify_code' => $verifyCode]
        )->firstOrFail();
        $customer->password = Hash::make($password);
        $customer->verify_code = null;
        $customer->save();
        return resSuccess();
    }

    /**
     * Lấy mã code để reset password
     * @param  string  $email
     * @return string|null
     */
    public function getVerifyCodeByEmail(string $email): ?string
    {
        /** @var User $customer */
        $customer = $this->getCustomerByEmail($email);
        if (!$customer) {
            return null;
        }
        $customer->verify_code = Str::upper(Str::random(5));
        $customer->save();
        SetVerifyCodeNullAfterTwoMinutes::dispatch($customer)->delay(now()->addMinutes(5));
        return $customer->verify_code;
    }

    /**
     * @param  string  $customerId
     * @param  string  $deliveryId
     */
    public function setDeliveryDefault(string $customerId, string $deliveryId)
    {
        if (!CustomerDelivery::query()->where('customer_id', $customerId)->exists()) {
            return;
        }
        CustomerDelivery::query()->where('customer_id', $customerId)->update(['is_default' => false]);
        CustomerDelivery::query()->findOrFail($deliveryId)->update(['is_default' => true]);
    }

    /**
     * @param  string  $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getCustomerById(string $id)
    {
        return Customer::query()->withoutGlobalScope(OrganizationScope::class)->findOrFail($id);
    }

    /**
     * @return int[]
     */
    public function getCustomerOffer(): array
    {
        $deposit = optional(
                (new ConfigService())->getResultFromValueByLevel((new CustomerService())->getLevelByCurrentUser())
            )->deposit ?? 80;
        return [
            'deposit' => $deposit
        ];
    }

    /**
     * Lấy số tiền trong ví
     * @return float
     */
    public function getBalanceAmount(): float
    {
        return optional(
                (new ReportCustomerService())->getReportCustomerCurrent()
            )->balance_amount ?? 0;
    }

    /**
     * @param  Customer  $customer
     */
    public function upgradeLevel(Customer $customer): void
    {
        $customer->level++;
        $customer->save();
        (new ReportService())->reportLevel($customer->level, $customer->level - 1);
    }

    /**
     * @return int
     */
    public function getLevelByCurrentUser(): int
    {
        return Auth::user()->level == 0 ? 1 : Auth::user()->level;
    }

    /**
     * Update last_order_at to current user
     */
    public function setLastOrderAtByCurrentUser(): void
    {
        Auth::user()->update(['last_order_at' => now()]);
    }

    public function assignUserToCustomer(&$userId, string $model)
    {
        $user = (new $model)::query()->where('status', 1)->orderBy('quantity')->first();
        $userId = null;
        if ($user) {
            $userId = $user->user_id;
            $user->increment('quantity');
        }
    }

}