<?php

namespace App\Http\Controllers\KhachThue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThongBaoChuTroYeuCauMoi;
use App\Mail\ThongBaoKhachThueChapNhan;
use App\Jobs\GuiThongBaoChuTroYeuCauMoi;
class YeuCauThueController extends Controller
{
    public function index()
    {
        $token = session('api_token');
        $apiUrl = rtrim(env('API_BASE_URL', 'http://127.0.0.1:8000'), '/');

        $response = Http::withToken($token)->get("$apiUrl/api/khach-thue/yeu-cau-thue");
        $yeuCauThue = $response->json('data') ?? [];

        return view('khachthue.yeucauthue.index', compact('yeuCauThue'));
    }

    public function store(Request $request)
    {
        $token = session('api_token');
        $apiUrl = rtrim(env('API_BASE_URL', 'http://127.0.0.1:8000'), '/');

        try {
            \Log::info('📨 [WEB] Gửi yêu cầu thuê phòng (v2)', [
                'user_id' => auth()->id(),
                'input' => $request->all(),
            ]);

            $validated = $request->validate([
                'bai_dang_id' => 'required|integer',
                'cccd' => 'required|digits_between:9,12',
                'ngay_bat_dau' => 'required|date',
                'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
                'tien_coc' => 'required|numeric|min:0',
                'ghi_chu' => 'nullable|string|max:500',
                'nguoi_than' => 'nullable|array',
                'file_hop_dong' => 'required|file|mimes:pdf|max:4096',
            ]);

            $response = Http::asMultipart()
                ->withToken($token)
                ->attach(
                    'file_hop_dong',
                    fopen($request->file('file_hop_dong')->getRealPath(), 'r'),
                    $request->file('file_hop_dong')->getClientOriginalName()
                )
                ->post("$apiUrl/api/khach-thue/yeu-cau-thue", [
                    ['name' => 'bai_dang_id', 'contents' => $validated['bai_dang_id']],
                    ['name' => 'cccd', 'contents' => $validated['cccd']],
                    ['name' => 'ngay_bat_dau', 'contents' => $validated['ngay_bat_dau']],
                    ['name' => 'ngay_ket_thuc', 'contents' => $validated['ngay_ket_thuc']],
                    ['name' => 'tien_coc', 'contents' => $validated['tien_coc']],
                    ['name' => 'ghi_chu', 'contents' => $validated['ghi_chu'] ?? ''],
                    ['name' => 'nguoi_than', 'contents' => !empty($validated['nguoi_than']) ? json_encode($validated['nguoi_than']) : ''],
                ]);


            if ($response->failed()) {
                $msg = $response->json('error') ?? 'Gửi yêu cầu thuê thất bại.';
                \Log::error('❌ [WEB] API phản hồi lỗi', ['body' => $response->body()]);
                return back()->with('error', $msg);
            }

            $data = $response->json('data') ?? [];
            $chuTro = (object) ($data['chu_tro'] ?? []);
            $phong = (object) ($data['phong'] ?? []);
            $dayTro = (object) ($data['phong']['day_tro'] ?? []);
            $khach = (object) session('user');

            if (!empty($chuTro->email)) {
                \Log::info('📧 [WEB] Gửi mail tới chủ trọ', [
                    'to' => $chuTro->email,
                    'chu_tro' => $chuTro,
                    'phong' => $phong,
                    'day_tro' => $dayTro,
                    'khach' => $khach,
                ]);

                GuiThongBaoChuTroYeuCauMoi::dispatch($chuTro, $khach, $phong, $dayTro);

                \Log::info('✅ [WEB] Mail gửi chủ trọ đã được đưa vào hàng đợi');
            }

            return back()->with('success', '✅ Đã gửi yêu cầu thuê phòng thành công!');

        } catch (\Throwable $e) {
            \Log::error('💥 [WEB] Lỗi khi xử lý yêu cầu thuê', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Lỗi máy chủ: ' . $e->getMessage());
        }
    }

    public function huy($id)
    {
        $token = session('api_token');
        $apiUrl = rtrim(env('API_BASE_URL', 'http://127.0.0.1:8000'), '/');

        $res = Http::withToken($token)->delete("$apiUrl/api/khach-thue/yeu-cau-thue/{$id}/huy");

        if ($res->failed()) {
            return back()->with('error', $res->json('error') ?? 'Không thể hủy yêu cầu thuê.');
        }

        return back()->with('success', 'Đã hủy yêu cầu thuê thành công!');
    }

}
