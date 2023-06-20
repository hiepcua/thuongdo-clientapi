<?php


namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class SortByCodeScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $column = 'code';
        $isColExist = Schema::hasColumn($model->getTable(), $column);
        if (!$isColExist) {
            return;
        }
        $builder->orderByDesc($column);
    }
}