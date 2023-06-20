<?php


namespace App\Scopes\Traits;


use App\Scopes\SortByCreatedDescScope;

trait HasSortDescByCreated
{
    public static function bootHasSortDescByCreated()
    {
        static::addGlobalScope(new SortByCreatedDescScope());
    }
}