<?php

namespace App\Http\Controllers\KhachThue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DanhGiaController extends Controller
{
    public function index()
    {
        $token = session('api_token');

        // Lấy danh sách đánh giá
        $resDanhGia = Http::withToken($token)->get(env('API_URL') . '/khach-thue/danh-gia');
        $danhGia = $resDanhGia->json('data') ?? [];

        // Lấy danh sách hợp đồng để chọn khi đánh giá
        $resHopDong = Http::withToken($token)->get(env('API_URL') . '/khach-thue/hop-dong');
        $hopDong = $resHopDong->json('data') ?? [];

        return view('khachthue.danhgia.index', compact('danhGia', 'hopDong'));
    }

    public function store(Request $request)
    {
        $token = session('api_token');

        $response = Http::withToken($token)->post(env('API_URL') . '/khach-thue/danh-gia', [
            'hop_dong_id' => $request->hop_dong_id,
            'diem_so' => $request->diem_so,
            'binh_luan' => $request->binh_luan,
        ]);

        if ($response->failed()) {
            return back()->with('error', $response->json('error') ?? 'Gửi đánh giá thất bại.');
        }

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá!');
    }
}
