<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThongBaoKhachChapNhan;


class YeuCauThueController_1 extends Controller
{
    public function __construct(private ApiClient $api)
    {
    }

    public function index()
    {
        $this->api->setToken(session('api_token'));
        $list = $this->api->get('/chu-tro/yeu-cau-thue') ?? [];
        return view('chutro.yeucauthue.index', ['yeu_cau' => $list]);
    }

    public function tuChoi($id)
    {
        $this->api->setToken(session('api_token'));
        $this->api->post("/chu-tro/yeu-cau-thue/{$id}/tu-choi", []);
        return back()->with('ok', 'Đã từ chối yêu cầu.');
    }

    /*public function chapNhan($id)
    {
        try {
            $chuTro = auth()->user();

            $yeuCau = \App\Models\YeuCauThue::with(['khachThue.nguoiDung', 'phong.dayTro'])
                ->where('id', $id)
                ->where('chu_tro_id', $chuTro->id)
                ->firstOrFail();

            if ($yeuCau->trang_thai !== 'cho_duyet') {
                return response()->json(['error' => 'Yêu cầu này không thể chấp nhận.'], 400);
            }

            // ✅ Cập nhật trạng thái
            $yeuCau->update(['trang_thai' => 'chap_nhan']);

            $khach = $yeuCau->khachThue?->nguoiDung;
            $phong = $yeuCau->phong;
            $dayTro = $phong?->dayTro;

            // ✅ Gửi mail cho khách thuê
            if ($khach && $khach->email) {
                \Mail::to($khach->email)->queue(
                    new \App\Mail\ThongBaoKhachChapNhan(
                        (object) [
                            'ho_ten' => $khach->ho_ten,
                            'email' => $khach->email,
                        ],
                        (object) [
                            'ten_phong' => $phong->so_phong ?? 'Không rõ',
                            'day_tro' => $dayTro->ten_day_tro ?? 'Không rõ',
                        ],
                        (object) [
                            'chu_tro' => $chuTro->ho_ten ?? 'Chủ trọ'
                        ]
                    )
                );
            }

            // ✅ Trả dữ liệu cho web controller
            return response()->json([
                'message' => 'Đã chấp nhận yêu cầu.',
                'khach' => [
                    'id' => $yeuCau->khach_thue_id,
                    'ho_ten' => $khach->ho_ten,
                    'email' => $khach->email,
                ],
                'phong' => [
                    'id' => $phong->id,
                    'ten_phong' => $phong->so_phong,
                    'day_tro' => $dayTro->ten_day_tro,
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error('💥 Lỗi chapNhan', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Lỗi máy chủ: ' . $e->getMessage()], 500);
        }
    }*/
    public function chapNhan($id)
    {
        $this->api->setToken(session('api_token'));

        // 👇 gọi API với flag only_accept để KHÔNG tạo hợp đồng ngay
        $res = $this->api->post("/chu-tro/yeu-cau-thue/{$id}/chap-nhan?only_accept=1", []);

        try {
            if (!empty($res['khach']) && !empty($res['phong'])) {
                $chuTro = session('user');
                $khach = $res['khach'];
                $phong = $res['phong'];

                // 📨 Gửi mail cho khách
                if (!empty($khach['email'])) {
                    Mail::to($khach['email'])->queue(
                        new \App\Mail\ThongBaoKhachThueChapNhan($khach, $chuTro, $phong)
                    );
                }

                // ✅ Sau đó mở form tạo hợp đồng
                return redirect()->route('chu-tro.hop-dong.create', [
                    'phong_id' => $phong['id'] ?? null,
                    'khach_thue_id' => $khach['id'] ?? null,
                    'yeu_cau_id' => $id,
                ])->with('ok', 'Đã chấp nhận yêu cầu. Vui lòng tạo hợp đồng.');
            }
        } catch (\Exception $e) {
            \Log::error('❌ Gửi mail thất bại: ' . $e->getMessage());
        }

        return back()->with('ok', 'Đã chấp nhận yêu cầu.');
    }


    public function create(\Illuminate\Http\Request $request)
    {
        $chu_tro_id = session('user')['id'];

        $phongs = DB::table('phong')
            ->join('day_tro', 'day_tro.id', '=', 'phong.day_tro_id')
            ->where('day_tro.chu_tro_id', $chu_tro_id)
            ->where('phong.trang_thai', 'trong')
            ->select('phong.id', 'phong.so_phong', 'day_tro.ten_day_tro')
            ->get();

        $khach_thues = DB::table('khach_thue')
            ->join('nguoi_dung', 'nguoi_dung.id', '=', 'khach_thue.nguoi_dung_id')
            ->select('khach_thue.id', 'nguoi_dung.ho_ten')
            ->get();

        // lấy prefill từ query (?phong_id=&khach_thue_id=)
        $selected_phong_id = $request->query('phong_id');
        $selected_khach_thue_id = $request->query('khach_thue_id');

        return view('chutro.hopdong.create', compact('phongs', 'khach_thues', 'selected_phong_id', 'selected_khach_thue_id'));
    }
    public function store(Request $request)
    {
        $token = session('api_token');
        $apiUrl = rtrim(env('API_BASE_URL', 'http://127.0.0.1:8000'), '/');

        $data = $request->validate([
            'bai_dang_id' => 'required|integer',
            'ghi_chu' => 'nullable|string|max:500',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'tien_coc' => 'nullable|numeric|min:0',
            'file_hop_dong' => 'nullable|file|mimes:pdf|max:4096',
            'nguoi_than' => 'nullable|array',
        ]);

        $response = Http::withToken($token)
            ->attach('file_hop_dong', file_get_contents($request->file('file_hop_dong')), $request->file('file_hop_dong')->getClientOriginalName())
            ->post("$apiUrl/api/khach-thue/yeu-cau-thue", $data);

        if ($response->failed()) {
            return back()->with('error', 'Gửi yêu cầu thất bại!');
        }

        return back()->with('success', '✅ Gửi yêu cầu thuê thành công!');
    }

}
