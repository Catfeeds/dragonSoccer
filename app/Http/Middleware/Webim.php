<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;
class Webim
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
        Session::put('url.intended',url("/webim/chat"));
        if (auth()->guard('adminusers')->check()) {
            return $next($request);
        }
        return  redirect('/webim/login');
        exit();
    }

   
}
