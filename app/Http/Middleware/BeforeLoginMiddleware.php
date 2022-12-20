<?php

namespace App\Http\Middleware;

use Closure;

class BeforeLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(session('logged_in')){
            return redirect('/');
        }
        return $next($request);
    }
}
