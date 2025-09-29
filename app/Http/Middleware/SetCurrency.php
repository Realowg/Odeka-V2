<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCurrency
{
    public function handle(Request $request, Closure $next)
    {
        $code = session('currency');
        if (auth()->check() && auth()->user()->preferred_currency) {
            $code = auth()->user()->preferred_currency;
        }
        if ($code) {
            app()->instance('display_currency', strtoupper($code));
        }
        return $next($request);
    }
}


