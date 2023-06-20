<?php


namespace App\Scopes\Traits;


use App\Scopes\SortByCreatedDescScope;

trait HasSortByCreated
{
    public static function bootHasSortByCreated()
    {
        static::addGlobalScope(new SortByCreatedDescScope());
    }
}