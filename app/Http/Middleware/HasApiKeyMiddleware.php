<?php

namespace App\Http\Middleware;

use App\Models\ApiUser;
use Closure;

class HasApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(ApiUser::first()==null)
        {
            return redirect()->route('index');
        }
        return $next($request);
    }
}
