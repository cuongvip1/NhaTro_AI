<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThongBaoKhachThueChapNhan;
use App\Mail\ThongBaoKhachThueTuChoi;
use Illuminate\Support\Facades\Log;

class YeuCauThueController extends Controller
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

    /**
     * ❌ Chủ trọ từ chối yêu cầu thuê
     */
    public function tuChoi($id)
    {
        $this->api->setToken(session('api_token'));
        $res = $this->api->post("/chu-tro/yeu-cau-thue/{$id}/tu-choi", []);

        try {
            if (!empty($res['khach']) && !empty($res['phong'])) {
                $chuTro = session('user');
                $khach = $res['khach'];
                $phong = $res['phong'];

                // 📨 Gửi mail cho khách thuê thông báo bị từ chối
                if (!empty($khach['email'])) {
                    Mail::to($khach['email'])->queue(
                        new ThongBaoKhachThueTuChoi($khach, $chuTro, $phong)
                    );
                    Log::info('📭 Đã gửi mail thông báo từ chối', ['to' => $khach['email']]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('💥 Gửi mail từ chối thất bại', ['error' => $e->getMessage()]);
        }

        return back()->with('ok', 'Đã từ chối yêu cầu thuê.');
    }

    /**
     * ✅ Chủ trọ chấp nhận yêu cầu → tạo hợp đồng luôn + gửi mail cho khách
     */
    public function chapNhan($id)
    {
        $this->api->setToken(session('api_token'));

        try {
            $res = $this->api->post("/chu-tro/yeu-cau-thue/{$id}/chap-nhan", []);

            if (!empty($res['hop_dong_id'])) {
                Log::info('✅ Chủ trọ đã tạo hợp đồng', ['hop_dong_id' => $res['hop_dong_id']]);
                return redirect()
                    ->route('chu-tro.hop-dong.show', $res['hop_dong_id'])
                    ->with('ok', 'Đã chấp nhận và tạo hợp đồng thành công!');
            }

            if (!empty($res['khach']) && !empty($res['phong'])) {
                $chuTro = session('user');
                $khach = $res['khach'];
                $phong = $res['phong'];

                if (!empty($khach['email'])) {
                    Mail::to($khach['email'])->queue(
                        new ThongBaoKhachThueChapNhan($khach, $chuTro, $phong)
                    );
                    Log::info('📧 Đã gửi mail chấp nhận cho khách thuê', ['to' => $khach['email']]);
                }

                return back()->with('ok', 'Đã chấp nhận yêu cầu thành công!');
            }

            return back()->with('error', $res['error'] ?? 'Không thể xử lý yêu cầu thuê.');
        } catch (\Throwable $e) {
            Log::error('💥 Lỗi chấp nhận yêu cầu thuê (WEB)', ['error' => $e->getMessage()]);
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $this->api->setToken(session('api_token'));
        $res = $this->api->get("/chu-tro/yeu-cau-thue/{$id}");

        if (!$res) {
            return back()->with('error', 'Không tìm thấy yêu cầu thuê.');
        }

        $data = $res['data'] ?? $res;

        return view('chutro.yeucauthue.show', ['yeu_cau' => $data]);
    }
public function xemHopDong($id)
{
    $this->api->setToken(session('api_token'));

    $res = $this->api->get("/chu-tro/yeu-cau-thue/{$id}/hop-dong-file");

    if (empty($res['file'])) {
        abort(404, 'Không tìm thấy hợp đồng');
    }

    return redirect(
        rtrim(env('API_BASE_URL'), '/') . '/storage/' . ltrim($res['file'], '/')
    );
}

}
