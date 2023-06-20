<?php


namespace App\Services;


use App\Models\User;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService extends BaseService
{
    /**
     * Lấy thông tin user qua email
     * @param  string  $email
     * @return Builder|Model|object|null
     */
    public function getUserByEmail(string $email)
    {
        return User::query()->withoutGlobalScope(OrganizationScope::class)->where('email', $email)->select(
            'id',
            'name',
            'email',
            'password',
            'login_failed',
            'blocked_at',
            'organization_id',
            'status'
        )->firstOrFail();
    }

    /**
     * Thay đổi mật khẩu
     * @param  string  $email
     * @param  string  $password
     * @return JsonResponse
     */
    public function changePassword(string $email, string $password): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUserByEmail($email);
        $user->password = Hash::make($password);
        $user->verify_code = null;
        $user->save();
        return resSuccess();
    }

    /**
     * @param  string  $model
     * @return mixed|null
     */
    public function getStaffAssign(string $model)
    {
        return optional((new $model)->query()->where('status', 1)->orderBy('quantity')->first())->user_id;
    }
}