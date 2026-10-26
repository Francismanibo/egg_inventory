<?php

namespace App\Http\Middleware;

use App\Models\CustomerAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $accountId = $request->session()->get('customer_account_id');

        if (! $accountId || ! CustomerAccount::query()->whereKey($accountId)->exists()) {
            $request->session()->forget('customer_account_id');
            $request->session()->put('customer.intended', $request->fullUrl());

            return redirect()->route('login');
        }

        return $next($request);
    }
}
