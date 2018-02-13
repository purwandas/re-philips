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
        if (Auth::check() && (Auth::user()->role->role_group == 'Admin' || Auth::user()->role->role_group == 'Master' || Auth::user()->role->role_group == 'Supervisor' || Auth::user()->role->role_group == 'Supervisor Hybrid') )
        {
            return $next($request);
        }

        return redirect('/');
    }
}
