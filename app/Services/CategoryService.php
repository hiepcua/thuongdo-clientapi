<?php


namespace App\Services;


use App\Http\Resources\OnlyIdNameResource;

class CategoryService extends BaseService
{
    protected string $_resource = OnlyIdNameResource::class;
}