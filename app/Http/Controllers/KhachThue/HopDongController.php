<?php

namespace App\Http\Controllers\KhachThue;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HopDongController extends Controller
{
    public function index()
    {
        $token = session('api_token');
        $apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');

        try {
            $response = Http::withToken($token)->get("$apiUrl/khach-thue/hop-dong");

            Log::info('📦 [HopDongController@index] API Response Status:', [
                'status' => $response->status(),
                'ok' => $response->ok(),
            ]);
            Log::info('📦 [HopDongController@index] API JSON:', $response->json() ?? []);

            $hopDong = $response->json('data');
            if (!$hopDong) {
                $hopDong = $response->json();
            }
            if (!is_array($hopDong)) {
                $hopDong = [];
            }

            return view('khachthue.hopdong.index', compact('hopDong'));
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi gọi API /khach-thue/hop-dong', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể kết nối đến máy chủ API.');
        }
    }

    public function show($id)
    {
        $token = session('api_token');
        $apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');

        try {
            $response = Http::withToken($token)->get("$apiUrl/khach-thue/hop-dong/{$id}");

            Log::info("📦 [HopDongController@show] API Response (ID {$id}):", [
                'status' => $response->status(),
                'data' => $response->json(),
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Không thể tải thông tin hợp đồng.');
            }

            $hopDong = $response->json('data');
            if (!$hopDong) {
                $hopDong = $response->json();
            }

            return view('khachthue.hopdong.show', compact('hopDong'));
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi gọi API /khach-thue/hop-dong/{id}', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể kết nối đến máy chủ API.');
        }
    }
}
