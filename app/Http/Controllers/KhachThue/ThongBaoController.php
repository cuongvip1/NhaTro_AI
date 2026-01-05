<?php

namespace App\Http\Controllers\KhachThue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ThongBaoController extends Controller
{
    public function index()
    {
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Phiên đăng nhập đã hết hạn.');
        }

        $response = Http::withToken($token)
            ->get(env('API_URL') . '/khach-thue/thong-bao');

        if (!$response->successful()) {
            session(['thong_bao_chua_doc' => 0, 'thong_bao_moi_nhat' => []]);

            return view('khachthue.thongbao.index', [
                'thongBao' => [],
            ])->with('error', 'Không lấy được danh sách thông báo.');
        }

        $thongBao = $response->json()['data'] ?? [];

        $soChuaDoc = collect($thongBao)->where('da_xem', 0)->count();
        session(['thong_bao_chua_doc' => $soChuaDoc]);

        $mini = collect($thongBao)
            ->sortByDesc('ngay_tao')
            ->take(5)
            ->values()
            ->toArray();

        session(['thong_bao_moi_nhat' => $mini]);

        $soDaDoc = collect($thongBao)->where('da_xem', 1)->count();

        return view('khachthue.thongbao.index', [
            'thongBao' => $thongBao,
            'soChuaDoc' => $soChuaDoc,
            'soDaDoc' => $soDaDoc,
        ]);
    }


    public function markAsRead($id, Request $request)
    {
        $token = session('api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên đăng nhập đã hết hạn.',
            ], 401);
        }

        $apiResponse = Http::withToken($token)
            ->post(env('API_URL') . "/khach-thue/thong-bao/{$id}/mark-as-read");

        if (!$apiResponse->successful()) {
            $body = $apiResponse->json();
            return response()->json([
                'success' => false,
                'message' => $body['error'] ?? 'Không thể đánh dấu đã xem.',
            ], $apiResponse->status());
        }

        $list = Http::withToken($token)
            ->get(env('API_URL') . '/khach-thue/thong-bao')
            ->json()['data'] ?? [];

        $soChuaDoc = collect($list)->where('da_xem', 0)->count();
        $mini = collect($list)->sortByDesc('ngay_tao')->take(5)->values()->toArray();

        session([
            'thong_bao_chua_doc' => $soChuaDoc,
            'thong_bao_moi_nhat' => $mini,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu thông báo là đã xem.',
        ]);
    }

    public function markAllRead(Request $request)
    {
        $token = session('api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên đăng nhập đã hết hạn.',
            ], 401);
        }

        $apiResponse = Http::withToken($token)
            ->post(env('API_URL') . '/khach-thue/thong-bao/mark-all-read');

        if (!$apiResponse->successful()) {
            $body = $apiResponse->json();
            return response()->json([
                'success' => false,
                'message' => $body['error'] ?? 'Không thể đánh dấu tất cả đã xem.',
            ], $apiResponse->status());
        }

        session([
            'thong_bao_chua_doc' => 0,
            'thong_bao_moi_nhat' => [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tất cả thông báo đã được đánh dấu là đã xem.',
        ]);
    }
    public function xoaDaDoc(Request $request)
    {
        $token = session('api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên đăng nhập đã hết hạn.',
            ], 401);
        }

        $apiResponse = Http::withToken($token)
            ->delete(env('API_URL') . '/khach-thue/thong-bao/xoa-da-doc');

        if (!$apiResponse->successful()) {
            $body = $apiResponse->json();
            return response()->json([
                'success' => false,
                'message' => $body['error'] ?? 'Không thể xóa thông báo đã đọc.',
            ], $apiResponse->status());
        }

        $list = Http::withToken($token)
            ->get(env('API_URL') . '/khach-thue/thong-bao')
            ->json()['data'] ?? [];

        $soChuaDoc = collect($list)->where('da_xem', 0)->count();
        $mini = collect($list)->sortByDesc('ngay_tao')->take(5)->values()->toArray();

        session([
            'thong_bao_chua_doc' => $soChuaDoc,
            'thong_bao_moi_nhat' => $mini,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa các thông báo đã đọc.',
        ]);
    }

}
