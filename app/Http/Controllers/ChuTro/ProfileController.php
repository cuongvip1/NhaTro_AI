<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiClient;
use Illuminate\Support\Arr;

class ProfileController extends Controller
{
    protected ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    /**
     * 🧩 Hiển thị hồ sơ cá nhân
     */
    public function show(Request $request)
    {
        $this->api->setToken(session('api_token'));

        // Gọi API lấy thông tin hồ sơ
        $profile = $this->api->get('chu-tro/profile');

        // Tránh lỗi nếu API không trả dữ liệu hợp lệ
        if (empty($profile) || !is_array($profile)) {
            $profile = session('user', []);
        }

        return view('chutro.profile', compact('profile'));
    }

    /**
     * Cập nhật hồ sơ (bao gồm upload ảnh)
     */
    public function update(Request $request)
    {
        $this->api->setToken(session('api_token'));

        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'anh_dai_dien' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $body = [
            'ho_ten' => $validated['ho_ten'],
            'so_dien_thoai' => $validated['so_dien_thoai'] ?? '',
        ];

        $files = [];
        if ($request->hasFile('anh_dai_dien')) {
            $files['anh_dai_dien'] = $request->file('anh_dai_dien');
        }

        $response = $this->api->postMultipart('chu-tro/update-profile', $body, $files);

        if (!empty($response['user'])) {
            $user = $response['user'];
            if (!empty($user['anh_dai_dien']) && !str_starts_with($user['anh_dai_dien'], 'http')) {
                $user['anh_dai_dien'] = rtrim(env('API_URL'), '/') . '/' . ltrim($user['anh_dai_dien'], '/');
            }

            session()->put('user', $user);
            session()->put('avatar_bust', time());
            session()->save();
        }

        return redirect()
            ->route('chu-tro.profile.show')
            ->with('success', 'Cập nhật hồ sơ thành công!');
    }
    /**
     *Hiển thị thông tin ngân hàng của chủ trọ
     */
    public function bankInfo(Request $request)
    {
        $this->api->setToken(session('api_token'));

        try {
            $response = $this->api->get('chu-tro/profile/bank');
            $bank = $response['data'] ?? [];

            return response()->json([
                'success' => true,
                'data' => $bank
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin ngân hàng.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin ngân hàng (web → gọi API backend)
     */
    public function updateBank(Request $request)
    {
        $this->api->setToken(session('api_token'));

        $validated = $request->validate([
            'bank_code' => 'required|string|max:20',
            'account_no' => 'required|string|max:30',
            'account_name' => 'required|string|max:100',
        ]);

        try {
            $response = $this->api->post('chu-tro/profile/bank', $validated);

            if (!empty($response['success'])) {
                return response()->json([
                    'success' => true,
                    'message' => $response['message'] ?? 'Đã lưu thông tin ngân hàng thành công!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Cập nhật thất bại.'
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gửi dữ liệu đến API.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
