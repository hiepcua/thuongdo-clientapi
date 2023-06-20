<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(CategoryService $service)
    {
        $this->_service = $service;
    }
}
