<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->_service = $service;
    }

    /**
     * @return mixed
     */
    public function me()
    {
        return resSuccessWithinData(
            Auth::user()->only(
                'id',
                'name',
                'email'
            )
        );
    }

    public function getUsersByRole(string $roleName): JsonResponse
    {
        $role = new Role(['guard_name' => 'admin']);
        return resSuccessWithinData(new ListResource($role->newQuery()->where('name', $roleName)->first()->users, UserResource::class));
    }
}
