<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckGoogleLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    // @author : Pakkapon Chomchoey 66160080
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Session::has('google_user')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
