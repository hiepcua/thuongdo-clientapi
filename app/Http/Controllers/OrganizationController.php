<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListResource;
use App\Http\Resources\Organization\OrganizationBankResource;
use App\Models\OrganizationBank;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function getBanks(): JsonResponse
    {
        return resSuccessWithinData(
            new ListResource(OrganizationBank::query()->get()->groupBy('bank_id')->values(), OrganizationBankResource::class)
        );
    }
}
