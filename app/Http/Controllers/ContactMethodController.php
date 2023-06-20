<?php

namespace App\Http\Controllers;

use App\Services\ContactMethodService;
use Illuminate\Http\Request;

class ContactMethodController extends Controller
{
    public function __construct(ContactMethodService $service)
    {
        $this->_service = $service;
    }
}
