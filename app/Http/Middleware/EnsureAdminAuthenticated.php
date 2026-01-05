<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\GenericUser;
use App\Support\Role;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user') || !session()->has('api_token')) {
            return redirect()->route('admin.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // Lấy thông tin user từ session (do API trả về)
        $sessionUser = session('user');

        // Kiểm tra role admin
        if (!Role::isAdmin($sessionUser)) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Bạn không đủ quyền để truy cập Admin']);
        }

        // Tạo user “ảo” từ dữ liệu session nếu chưa có
        if (!Auth::check()) {
            $user = new GenericUser($sessionUser);
            Auth::setUser($user);
        }

        return $next($request);
    }
}
