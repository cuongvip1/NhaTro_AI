<?php

namespace App\Http\Controllers\KhachThue;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $token = session('api_token');
        $apiUrl = env('API_URL');

        try {
            $hopDongRes = Http::withToken($token)->get("$apiUrl/khach-thue/hop-dong");
            $hoaDonRes = Http::withToken($token)->get("$apiUrl/khach-thue/hoa-don");
            $thongBaoRes = Http::withToken($token)->get("$apiUrl/khach-thue/thong-bao");

            $hopDong = $hopDongRes->json('data') ?? [];
            $hoaDon = $hoaDonRes->json('data') ?? [];
            $thongBao = $thongBaoRes->json('data') ?? [];

            $hopDong = collect($hopDong)->map(function ($item) {
                if (is_string($item)) {
                    $decoded = json_decode($item, true);
                    return is_array($decoded) ? $decoded : [];
                }
                return $item;
            })->toArray();

            $soChuaDoc = collect($thongBao)->where('da_xem', 0)->count();
            session(['thong_bao_chua_doc' => $soChuaDoc]);

        } catch (\Exception $e) {
            report($e);
            $hopDong = $hoaDon = $thongBao = [];

            session(['thong_bao_chua_doc' => 0]);
        }

        return view('khachthue.dashboard', [
            'hopDong' => $hopDong,
            'hoaDon' => $hoaDon,
            'thongBao' => $thongBao,
        ]);
    }

}
