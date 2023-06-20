<?php

namespace App\Http\Middleware;

use App\Services\CustomerService;
use Closure;
use Illuminate\Http\Request;

class Extension
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $customerId = request()->header('x-client');
        (new CustomerService())->getCustomerById($customerId);
        request()->request->add(['customer_id' => $customerId]);
        return $next($request);
    }
}
