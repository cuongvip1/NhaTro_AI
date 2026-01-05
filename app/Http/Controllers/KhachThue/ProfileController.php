<?php

namespace App\Http\Controllers\KhachThue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa hồ sơ
     */
    public function edit()
    {
        $token = session('api_token');
        $api = env('API_URL', 'http://127.0.0.1:8000/api');

        try {
            $response = Http::withToken($token)->get("$api/khach-thue/profile");

            if ($response->failed()) {
                return back()->with('error', 'Không thể tải thông tin hồ sơ.');
            }

            $user = $response->json();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Không thể kết nối đến máy chủ API.');
        }

        return view('khachthue.profile.edit', compact('user'));
    }

    /**
     * Cập nhật hồ sơ khách thuê
     */
    public function update(Request $request)
    {
        $token = session('api_token');
        $api = env('API_URL', 'http://127.0.0.1:8000/api');

        try {
            $http = Http::withToken($token);

            // Nếu có ảnh mới, gửi kèm file multipart
            if ($request->hasFile('anh_dai_dien')) {
                $file = $request->file('anh_dai_dien');
                $http = $http->attach(
                    'anh_dai_dien',
                    file_get_contents($file),
                    $file->getClientOriginalName()
                );
            }

            $response = $http->asMultipart()->post("$api/khach-thue/profile", [
                ['name' => 'ho_ten', 'contents' => $request->ho_ten],
                ['name' => 'so_dien_thoai', 'contents' => $request->so_dien_thoai],
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Cập nhật hồ sơ thất bại.');
            }

            $updatedProfile = Http::withToken($token)->get("$api/khach-thue/profile")->json();

            session([
                'user' => $updatedProfile,
                'avatar_bust' => time(),
            ]);

            return redirect()
                ->route('khach-thue.profile.edit')
                ->with('success', 'Cập nhật hồ sơ thành công!');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Lỗi khi kết nối đến máy chủ API.');
        }
    }
}
