<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $version
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $version = 'v1')
    {
        config(['app.api.version' => $version]);
        
        return $next($request);
    }
}

