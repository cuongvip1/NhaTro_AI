<?php

namespace App\Http\Controllers\KhachThue;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhongController extends Controller
{
    public function index()
    {
        $token = session('api_token');
        $api = env('API_URL', 'http://127.0.0.1:8000/api');

        try {
            $response = Http::withToken($token)->acceptJson()->get("$api/khach-thue/phong");

            Log::info('📦 [PhongController@index] API Response:', [
                'status' => $response->status(),
                'json' => $response->json(),
            ]);

            $json = $response->json();
            $phong = $json['data'] ?? $json;

            if (!is_array($phong)) {
                $phong = [];
            }

            return view('khachthue.phong.index', compact('phong'));
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi gọi API phòng khách thuê', ['error' => $e->getMessage()]);
            return back()->with('error', 'Không thể tải danh sách phòng.');
        }
    }

    public function show($id)
    {
        $token = session('api_token');
        $api = env('API_URL', 'http://127.0.0.1:8000/api');

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get("$api/khach-thue/phong/{$id}");

            if ($response->failed()) {
                return back()->with('error', 'Không thể tải thông tin phòng.');
            }

            // ✅ Lấy đúng phần data (tránh lỗi cấp JSON)
            $json = $response->json();
            $phong = $json['data'] ?? $json;

            Log::info("📦 [PhongController@show] Dữ liệu phòng #{$id}:", $phong);

            return view('khachthue.phong.show', compact('phong'));
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi gọi API phòng chi tiết', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể kết nối đến máy chủ API.');
        }
    }
}
