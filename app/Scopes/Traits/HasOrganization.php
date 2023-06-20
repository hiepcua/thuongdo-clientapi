<?php


namespace App\Scopes\Traits;


use App\Scopes\OrganizationScope;

trait HasOrganization
{
    public static function bootHasOrganization()
    {
        static::addGlobalScope(new OrganizationScope());
    }
}