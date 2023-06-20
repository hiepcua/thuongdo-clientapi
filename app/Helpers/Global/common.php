<?php

use App\Exceptions\CustomValidationException;
use App\Services\OrganizationService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

if (!function_exists('getModelNameByClass')) {
    function getModelNameByClass($class): string
    {
        $strArr = explode('\\', $class);
        return array_pop($strArr);
    }
}

if (!function_exists('enoughMoneyToPay')) {
    /**
     * @param  bool  $isCheck
     * @throws Throwable
     */
    function enoughMoneyToPay(bool $isCheck)
    {
        throw_if(
            $isCheck,
            CustomValidationException::class,
            [trans('transaction.enough_money_to_pay')]
        );
    }
}

if (!function_exists('throwIfCustom')) {
    /**
     * @param  bool  $isCheck
     * @param  string  $msg
     * @throws Throwable
     */
    function throwIfCustom(bool $isCheck, string $msg)
    {
        throw_if(
            $isCheck,
            CustomValidationException::class,
            [$msg]
        );
    }
}

if (!function_exists('removeKeyNotExistsModel')) {
    function removeKeyNotExistsModel(array &$item, array $array)
    {
        $diffs = array_diff(array_keys($item), $array);
        if(!isset($item['id'])) $item['id'] = getUuid();
        if(!isset($item['created_at'])) $item['created_at'] = now();
        if (!$diffs) {
            return;
        }
        $diffs = array_values($diffs);

        foreach ($diffs as $diff) {
            unset($item[$diff]);
        }
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser(): ?Authenticatable
    {
        return Auth::user();
    }
}

if (!function_exists('getCurrentUserId')) {
    function getCurrentUserId(): string
    {
        return getCurrentUser()->id;
    }
}

if (!function_exists('getUuid')) {
    /**
     * @return string
     */
    function getUuid(): string
    {
        return Uuid::uuid6()->toString();
    }
}

if (!function_exists('getOrganization')) {
    /**
     * @return string
     */
    function getOrganization(): string
    {
        return request()->input('organization_id') ?? getCurrentUser(
            )->organization_id ?? (new OrganizationService())->getOrganizationDefault();
    }
}