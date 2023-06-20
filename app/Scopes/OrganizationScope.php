<?php


namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $organization = request()->header('x-organization');
        if (!$organization) {
            return;
        }
        $condition = [
            $model->getTable() . '.organization_id' => $organization
        ];
        request()->request->add($condition);
        $builder->where($condition);
        unset($condition);
    }
}