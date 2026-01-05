<?php

namespace App\Http\Controllers\KhachThue;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HoaDonController extends Controller
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(env('API_URL', 'http://127.0.0.1:8000/api'), '/');
    }

    /**
     * 🔹 Danh sách hóa đơn của khách thuê đang đăng nhập
     */
    public function index()
    {
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại.');
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiUrl}/khach-thue/hoa-don");

            if ($response->unauthorized()) {
                session()->forget('api_token');
                return redirect()->route('login')->with('error', 'Phiên đăng nhập đã hết hạn.');
            }

            if ($response->failed()) {
                Log::error('❌ Lỗi khi gọi API danh sách hóa đơn khách thuê', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return back()->with('error', 'Không thể tải danh sách hóa đơn.');
            }

            $data = $response->json();
            $hoaDon = $data['data'] ?? [];

            // Đảm bảo luôn là mảng
            if (!is_array($hoaDon)) {
                $hoaDon = json_decode(json_encode($hoaDon), true) ?? [];
            }

            return view('khachthue.hoadon.index', compact('hoaDon'));
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi kết nối API hóa đơn khách thuê', [
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Không thể kết nối đến máy chủ.');
        }
    }
    public function show($id)
    {
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại.');
        }

        try {
            $response = Http::withToken($token)->get("{$this->apiUrl}/khach-thue/hoa-don/{$id}");

            if ($response->unauthorized()) {
                session()->forget('api_token');
                return redirect()->route('login')->with('error', 'Phiên đăng nhập đã hết hạn.');
            }

            if ($response->failed()) {
                Log::error("❌ Lỗi tải chi tiết hóa đơn #{$id}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return back()->with('error', 'Không thể tải thông tin hóa đơn.');
            }

           $json = $response->json();
$chiTiet = $json['data'] ?? null;

if (!$chiTiet) {
    Log::error("❌ API trả về không có data khi xem hóa đơn #{$id}", [
        'response' => $json
    ]);
    return back()->with('error', 'Không có dữ liệu hóa đơn.');
}

               $chiTiet['chu_tro'] = array_merge(
    $chiTiet['chu_tro'] ?? [],
    [
        'ho_ten'        => data_get($chiTiet, 'chu_tro.ho_ten', 'Chưa cập nhật'),
        'so_dien_thoai' => data_get($chiTiet, 'chu_tro.so_dien_thoai', 'Chưa cập nhật'),
        'bank_code'     => data_get($chiTiet, 'chu_tro.bank_code', 'MB'),
        'account_no'    => data_get($chiTiet, 'chu_tro.account_no', '0000000000'),
        'account_name'  => strtoupper(
            data_get(
                $chiTiet,
                'chu_tro.account_name',
                data_get($chiTiet, 'chu_tro.ho_ten', 'TEN CHU TRO')
            )
        ),
    ]
);



            return view('khachthue.hoadon.show', compact('chiTiet'));

        } catch (\Throwable $e) {
            Log::error("❌ Lỗi khi kết nối đến API hóa đơn #{$id}", [
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Không thể kết nối đến máy chủ.');
        }
    }

}
