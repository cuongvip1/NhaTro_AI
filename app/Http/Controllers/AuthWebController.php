<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthWebController extends Controller
{
    protected ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    // =======================
    // 🧩 HIỂN THỊ FORM LOGIN
    // =======================
    public function showLogin()
    {
        return view('auth.login');
    }

    // =======================
    // XỬ LÝ ĐĂNG NHẬP
    // =======================
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'mat_khau' => 'required|string|min:3',
        ]);

        $apiUrl = rtrim(config('services.api.base'), '/');

        try {
            $response = Http::timeout(10)
                ->post("$apiUrl/auth/login", $credentials)
                ->json();
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Không thể kết nối API: ' . $e->getMessage());
        }

        if (empty($response['token'])) {
            return back()->with('error', 'Sai email hoặc mật khẩu!')->withInput();
        }

        $token = $response['token'];

        // 🧠 Lưu token vào session (và giữ lại khi reload)
        session([
            'api_token' => $token,
            'js_token' => $token, // thêm dòng này
        ]);
        session()->save();

        // 🔹 Lấy thông tin người dùng
        try {
            $profile = Http::withToken($token)->timeout(10)->get("$apiUrl/me")->json();
        } catch (\Throwable $e) {
            return redirect('/')->with('error', 'Không thể tải thông tin người dùng: ' . $e->getMessage());
        }

        if (!$profile || !is_array($profile)) {
            return redirect('/')->with('error', 'Không lấy được thông tin người dùng.');
        }

        if (empty($profile['anh_dai_dien'])) {
            $profile['anh_dai_dien'] = '/images/default-avatar.png';
        }

        session([
            'user' => $profile,
            'avatar_bust' => time(),
        ]);

        $role = $profile['vai_tro'] ?? 'nguoi_dung';
        $messages = [
            'quan_tri' => '👑 Chào mừng Quản trị viên!',
            'chu_tro' => '🏠 Xin chào Chủ trọ!',
            'khach_thue' => '🎉 Chào mừng bạn trở lại!',
            'nguoi_dung' => '🎉 Đăng nhập thành công!',
        ];

        return redirect('/')->with('ok', $messages[$role] ?? $messages['nguoi_dung']);
    }


    // =======================
    // 🚪 ĐĂNG XUẤT
    // =======================
    /*public function logout(Request $request)
    {
        try {
            if (session('api_token')) {
                $this->api->setToken(session('api_token'));
                $this->api->post('auth/logout');
            }
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi logout API', ['error' => $e->getMessage()]);
        }

        session()->flush();
        Cookie::queue(Cookie::forget('api_token'));
        echo "<script>localStorage.removeItem('token');</script>";
        return redirect('/')->with('ok', 'Đã đăng xuất!');
    }*/
        public function logout(Request $request)
{
    // 1️⃣ Gọi API logout (nếu có token)
    try {
        if (session()->has('api_token')) {
            $this->api->setToken(session('api_token'));
            $this->api->post('auth/logout');
        }
    } catch (\Throwable $e) {
        Log::warning('API logout failed', ['error' => $e->getMessage()]);
    }

    // 2️⃣ XÓA SẠCH SESSION (QUAN TRỌNG NHẤT)
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // 3️⃣ XÓA COOKIE nếu có
    Cookie::queue(Cookie::forget('api_token'));

    // 4️⃣ XÓA TOKEN TRONG JS (CÁCH ĐÚNG)
    return redirect('/')
        ->with('ok', 'Đã đăng xuất!')
        ->withCookie(cookie()->forget('api_token'));
}


    // ======================
    // FORM ĐĂNG KÝ
    // =======================
    public function showRegister()
    {
        return view('auth.register');
    }

    // =======================
    // XỬ LÝ ĐĂNG KÝ
    // =======================
    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string|max:20',
            // 'cccd' => $request->input('role') === 'khach_thue'
            // ? 'required|string|max:20'
            //: 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:khach_thue,chu_tro,quan_tri',
        ]);

        $payload = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            //'cccd' => $data['cccd'] ?? null,
            'password' => $data['password'],
            'password_confirmation' => $request->input('password_confirmation'),
            'role' => $data['role'],
        ];

        $apiUrl = rtrim(config('services.api.base'), '/');

        try {
            $response = Http::timeout(10)->post("$apiUrl/auth/register", $payload);

            if ($response->failed()) {
                $msg = $response->json('message') ?? 'Lỗi không xác định từ API.';
                return back()->with('error', '❌ Đăng ký thất bại: ' . $msg)->withInput();
            }

            return redirect('/login')->with('ok', '🎉 Đăng ký thành công! Hãy đăng nhập.');

        } catch (\Throwable $e) {
            return back()->with('error', '⚠️ Không thể kết nối API: ' . $e->getMessage())->withInput();
        }
    }
}
