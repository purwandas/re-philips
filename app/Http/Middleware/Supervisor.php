<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Supervisor
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
        if (Auth::check() && (Auth::user()->role == 'Admin' || Auth::user()->role == 'Master' || Auth::user()->role == 'Supervisor' || Auth::user()->role == 'Supervisor Hybrid') )
        {
            return $next($request);
        }

        return redirect('/');
    }
}
