<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\GenericUser;

class EnsureWebAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user') || !session()->has('api_token')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // Lấy thông tin user từ session (do API trả về)
        $sessionUser = session('user');

        // Tạo user “ảo” từ dữ liệu session
        if (!Auth::check()) {
            $user = new GenericUser($sessionUser);
            Auth::setUser($user);
        }

        return $next($request);
    }
}
