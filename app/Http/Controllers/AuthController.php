<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Requests\Auth\VerifyCodeEmailRequest;
use App\Mail\ForgotPassword;
use App\Models\Customer;
use App\Scopes\OrganizationScope;
use App\Services\AuthService;
use App\Services\CustomerService;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class AuthController
 * @package App\Http\Controllers
 *
 * @property AuthService $_service
 * @property CustomerService $_customerService
 */
class AuthController extends Controller
{
    private CustomerService $_customerService;

    public function __construct(AuthService $service, CustomerService $customerService)
    {
        $this->_service = $service;
        $this->_customerService = $customerService;
    }

    /**
     * Đăng nhập
     * @param  LoginRequest  $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function signIn(LoginRequest $request): JsonResponse
    {
        return $this->_service->signIn($request->input('username'), $request->input('password'));
    }

    /**
     * Đăng xuất
     * @return JsonResponse
     */
    public function signOut(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();
        return resSuccess(__('auth.logout'));
    }

    /**
     * Gửi code vào email người dùng để đặt lại mật khẩu
     * @param  string  $email
     * @return JsonResponse
     * @throws \Throwable
     */
    public function resetPasswordSendMail(string $email): JsonResponse
    {
        $this->_customerService->getCustomerByEmail($email);
        Mail::to($email)->queue(new ForgotPassword($this->_customerService->getVerifyCodeByEmail($email)));
        return resSuccess(__('auth.forgot_password_sent_email'));
    }

    /**
     * Confirm mã xác nhận
     * @param  VerifyCodeEmailRequest  $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function confirmVerifyCodeAndEmail(VerifyCodeEmailRequest $request): JsonResponse
    {
        /** @var Customer $customer */
        Customer::query()->withoutGlobalScope(OrganizationScope::class)->where(
            $request->only('email', 'verify_code')
        )->firstOrFail();
        return resSuccess();
    }

    /**
     * Thay đổi mật khẩu dựa trên email và mã xác minh.
     * @param  ChangePasswordRequest  $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        return $this->_customerService->changePassword(
            $request->input('email'),
            $request->input('verify_code'),
            $request->input('password')
        );
    }

    /**
     * Đăng ký
     * @param  SignUpRequest  $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function signUp(SignUpRequest $request): JsonResponse
    {
        $params = $request->all();
        $params['organization_id'] = getOrganization();
        return $this->_service->signUp($params);
    }

}
