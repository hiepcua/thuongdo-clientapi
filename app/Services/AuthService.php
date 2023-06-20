<?php


namespace App\Services;


use App\Constants\CustomerConstant;
use App\Exceptions\ResponseException;
use App\Helpers\RandomHelper;
use App\Models\Customer;
use App\Models\ReportConsignment;
use App\Models\ReportCustomer;
use App\Models\ReportUserCounselingCustomer;
use App\Scopes\OrganizationScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService
{
    /**
     * @description: Đăng nhập sử dụng email và password
     * @param  array  $credentials
     * @return JsonResponse
     * @throws ResponseException
     */
    public function signIn(string $username, string $password): JsonResponse
    {
        /** @var Customer $customer */
        $customer = Customer::query()->withoutGlobalScope(OrganizationScope::class)->where('email', $username)->orWhere(
            'phone_number',
            $username
        )->firstOrFail();
        if ($customer->status == CustomerConstant::CUSTOMER_STATUS_INACTIVE_KEY) {
            return resError(trans('auth.blocked'));
        }
        if (!Hash::check($password, $customer->password)) {
            return resError(trans('auth.failed'));
        }
        $email = $customer->email;
        Auth::login($customer);
        return resSuccessWithinData(
            [
                'token' => $customer->createToken($email)->plainTextToken,
                'user' => $customer->only(['id', 'name', 'email', 'organization_id'])
            ]
        );
        // Sai mật khẩu
        throw new ResponseException(trans('auth.password'));
    }

    /**
     * Đăng ký sau đó đăng nhập
     *
     * @param  array  $data
     * @return JsonResponse
     * @throws \Throwable
     */
    public function signUp(array $data): JsonResponse
    {
        $customerService = new CustomerService();
        $customerService->assignUserToCustomer($userCounselor, ReportUserCounselingCustomer::class);
        $customer = Customer::query()->create(
            array_merge(
                $data,
                [
                    'password' => Hash::make($data['password']),
                    'code' => RandomHelper::customerCode(),
                    'staff_counselor_id' => $userCounselor
                ]
            )
        );
        ReportCustomer::query()->create(['customer_id' => $customer->id]);
        ReportConsignment::query()->create(
            ['customer_id' => $customer->id, 'organization_id' => (new OrganizationService())->getOrganizationDefault()]
        );
        return $this->signIn($data['email'], $data['password']);
    }
}