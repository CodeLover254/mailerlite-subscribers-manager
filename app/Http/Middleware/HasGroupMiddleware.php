<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;

class HasGroupMiddleware
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
        if(Group::first()==null){
            return redirect()->route('show-add-group');
        }
        return $next($request);
    }
}
