<?php


namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CustomerCurrentScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $isColExist = Schema::hasColumn($model->getTable(),'customer_id');
        if(!$isColExist) return;
        $builder->where($model->getTable().'.customer_id', Auth::user()->id);
    }
}