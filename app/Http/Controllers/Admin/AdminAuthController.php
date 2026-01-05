<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Support\Role;

class AdminAuthController extends Controller
{
    /**
     * Hiển thị form login
     */
    public function showLogin()
    {
        // Nếu đã có token, redirect về dashboard
        if (session('api_token')) {
            return redirect()->route('admin.dashboard');
        }
        
        // Use Vietnamese-structured login view
        return view('admin.dang-nhap.login');
    }

    /**
     * Xử lý login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mat_khau' => 'required'
        ]);

        try {
            $apiBase = config('services.api.base');
            $baseUrl = str_replace('/api', '', $apiBase);
            
            $response = Http::post($baseUrl . '/api/auth/login', [
                'email' => $request->email,
                'mat_khau' => $request->mat_khau
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? null;
                $user = $data['user'] ?? null;
                
                if ($token && $user) {
                    // Kiểm tra vai trò phải là 'quan_tri' (quản trị)
                    if (!Role::isAdmin($user)) {
                        return back()->withErrors([
                            'email' => 'Bạn không đủ quyền để đăng nhập'
                        ])->withInput();
                    }

                    // Lưu token và thông tin user vào session (dùng key 'user' để middleware chung nhận)
                    session([
                        'api_token' => $token,
                        'user' => $user,
                        'admin_user' => $user,
                    ]);

                    return redirect()->route('admin.dashboard')
                        ->with('success', 'Đăng nhập thành công!');
                }
            }
            
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng'
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Lỗi kết nối API: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->forget(['api_token', 'admin_user', 'user']);
        return redirect()->route('admin.login')
            ->with('success', 'Đã đăng xuất');
    }
}
