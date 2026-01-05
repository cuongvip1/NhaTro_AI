<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YeuThichWebController extends Controller
{
    public function index(Request $request)
    {
        $token = session('api_token') ?? $_COOKIE['api_token'] ?? null;

        if (!$token) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để xem danh sách yêu thích.');
        }

        $apiUrl = rtrim(config('services.api.base'), '/');

        try {
            Log::info('📡 [YeuThichWebController] gọi API', [
                'url' => "$apiUrl/khach-thue/yeu-thich",
                'token' => substr($token, 0, 15) . '...',
            ]);

            $response = Http::withToken($token)
                ->timeout(10)
                ->get("$apiUrl/khach-thue/yeu-thich");

            if ($response->failed()) {
                $status = $response->status();

                if ($status === 401) {
                    session()->forget('api_token');
                    return redirect('/login')->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                }

                return back()->with('error', "Không thể tải danh sách yêu thích. (Lỗi $status)");
            }

            $data = $response->json() ?? [];
            $baiDangList = $data['data'] ?? [];

            return view('khachthue.yeu-thich', compact('baiDangList'));

        } catch (\Throwable $e) {
            Log::error('❌ [YeuThichWebController] Lỗi kết nối API', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.');
        }
    }

    public function add($baiDangId)
    {
        $token = session('api_token') ?? $_COOKIE['api_token'] ?? null;

        if (!$token) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để thêm vào yêu thích.');
        }

        $apiUrl = rtrim(config('services.api.base'), '/');

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->post("$apiUrl/khach-thue/yeu-thich/$baiDangId");

            if ($response->failed()) {
                return back()->with('error', 'Không thể thêm vào yêu thích.');
            }

            return back()->with('success', 'Đã thêm vào danh sách yêu thích!');
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi thêm yêu thích', ['msg' => $e->getMessage()]);
            return back()->with('error', 'Lỗi kết nối đến máy chủ.');
        }
    }

    public function remove($baiDangId)
    {
        $token = session('api_token') ?? $_COOKIE['api_token'] ?? null;

        if (!$token) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để xóa khỏi yêu thích.');
        }

        $apiUrl = rtrim(config('services.api.base'), '/');

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->delete("$apiUrl/khach-thue/yeu-thich/$baiDangId");

            if ($response->failed()) {
                return back()->with('error', 'Không thể xóa khỏi yêu thích.');
            }

            return back()->with('success', 'Đã xóa khỏi danh sách yêu thích!');
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi xóa yêu thích', ['msg' => $e->getMessage()]);
            return back()->with('error', 'Lỗi kết nối đến máy chủ.');
        }
    }
}
