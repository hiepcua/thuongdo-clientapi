<?php


namespace App\Scopes\Traits;


use App\Scopes\SortByCodeScope;

trait HasSortByCode
{
    public static function bootHasSortByCreated()
    {
        static::addGlobalScope(new SortByCodeScope());
    }
}