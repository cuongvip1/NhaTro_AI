<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class WebAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('api_token')) {
            return $next($request);
        }
        if ($token = $request->cookie('api_token')) {
            session(['api_token' => $token]);

            return $next($request);
        }

        return redirect()
            ->route('login')
            ->with('err', 'Vui lòng đăng nhập');
    }
}
