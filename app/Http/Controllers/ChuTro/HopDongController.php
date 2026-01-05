<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HopDongController extends Controller
{
    private ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $this->api->setToken(session('api_token'));
        $chu_tro_id = session('user')['id'] ?? null;
        $day_tro_id = $request->input('day_tro_id');

        $day_tros = DB::table('day_tro')
            ->where('chu_tro_id', $chu_tro_id)
            ->select('id', 'ten_day_tro')
            ->get();

        try {
            $hop_dong = $this->api->get('/chu-tro/hop-dong', ['day_tro_id' => $day_tro_id]) ?? [];
            foreach ($hop_dong as &$hd) {
                if (!empty($hd['ngay_ket_thuc'])) {
                    $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($hd['ngay_ket_thuc']), false);
                    $hd['sap_het_han'] = $daysLeft <= 7 && $daysLeft >= 0;
                    $hd['con_lai'] = $daysLeft;
                }
            }
        } catch (\Throwable $e) {
            Log::error('🟥 Lỗi khi tải hợp đồng: ' . $e->getMessage());
            $hop_dong = [];
        }

        return view('chutro.hopdong.index', [
            'hop_dong' => json_decode(json_encode($hop_dong), true),
            'day_tros' => $day_tros
        ]);

    }
    public function create(Request $request)
    {
        $chu_tro_id = session('user')['id'];
        $yeu_cau_thue_id = $request->input('yeu_cau_thue_id');
        $yeu_cau = null;

        if ($yeu_cau_thue_id) {
            $yeu_cau = DB::table('yeu_cau_thue')
                ->join('bai_dang', 'yeu_cau_thue.bai_dang_id', '=', 'bai_dang.id')
                ->join('phong', 'bai_dang.phong_id', '=', 'phong.id')
                ->join('day_tro', 'phong.day_tro_id', '=', 'day_tro.id')
                ->join('khach_thue', 'yeu_cau_thue.khach_thue_id', '=', 'khach_thue.id')
                ->join('nguoi_dung', 'khach_thue.nguoi_dung_id', '=', 'nguoi_dung.id')
                ->where('day_tro.chu_tro_id', $chu_tro_id)
                ->where('yeu_cau_thue.id', $yeu_cau_thue_id)
                ->select([
                    'yeu_cau_thue.id as id',
                    'phong.id as phong_id',
                    'phong.so_phong',
                    'nguoi_dung.ho_ten as ten_khach_thue',
                    'khach_thue.id as khach_thue_id',
                ])
                ->first();

            if (!$yeu_cau) {
                return back()->with('error', 'Không tìm thấy yêu cầu thuê hợp lệ hoặc không thuộc chủ trọ này.');
            }
        }
        /*$phongs = DB::table('phong')
            ->join('day_tro', 'day_tro.id', '=', 'phong.day_tro_id')
            ->where('day_tro.chu_tro_id', $chu_tro_id)
            ->where('phong.trang_thai', 'trong')
            ->select('phong.id', 'phong.so_phong', 'day_tro.ten_day_tro')
            ->get();*/
        $phongs = DB::table('phong')
            ->join('day_tro', 'day_tro.id', '=', 'phong.day_tro_id')
            ->where('day_tro.chu_tro_id', $chu_tro_id)
            ->select('phong.id', 'phong.so_phong', 'day_tro.ten_day_tro', 'phong.trang_thai')
            ->when($yeu_cau, function ($q) use ($yeu_cau) {
                $q->orWhere('phong.id', $yeu_cau->phong_id);
            })
            ->get();


        $khach_thues = DB::table('khach_thue')
            ->join('nguoi_dung', 'nguoi_dung.id', '=', 'khach_thue.nguoi_dung_id')
            ->where(function ($q) use ($chu_tro_id) {
                $q->whereIn('khach_thue.id', function ($sub) use ($chu_tro_id) {
                    $sub->select('yt.khach_thue_id')
                        ->from('yeu_cau_thue as yt')
                        ->join('bai_dang as b', 'yt.bai_dang_id', '=', 'b.id')
                        ->join('phong as p', 'b.phong_id', '=', 'p.id')
                        ->join('day_tro as d', 'p.day_tro_id', '=', 'd.id')
                        ->where('d.chu_tro_id', $chu_tro_id);
                })
                    ->orWhereIn('khach_thue.id', function ($sub) use ($chu_tro_id) {
                        $sub->select('hd.khach_thue_id')
                            ->from('hop_dong as hd')
                            ->join('phong as p', 'hd.phong_id', '=', 'p.id')
                            ->join('day_tro as d', 'p.day_tro_id', '=', 'd.id')
                            ->where('d.chu_tro_id', $chu_tro_id);
                    });
            })
            ->select('khach_thue.id', 'nguoi_dung.ho_ten')
            ->distinct()
            ->orderBy('nguoi_dung.ho_ten')
            ->get();

        return view('chutro.hopdong.create', compact('phongs', 'khach_thues', 'yeu_cau'));
    }


    public function store(Request $request)
    {
        $this->api->setToken(session('api_token'));

        $validated = $request->validate([
            'phong_id' => 'required|integer',
            'khach_thue_id' => 'required|integer',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'tien_coc' => 'required|numeric|min:0',
            'file_hop_dong' => 'nullable|file|mimes:pdf|max:4096',
            'yeu_cau_thue_id' => 'nullable|integer',

            'nguoi_than' => 'nullable|array',
            'nguoi_than.*.ho_ten' => 'required|string|max:100',
            'nguoi_than.*.so_dien_thoai' => 'nullable|string|max:20',
            'nguoi_than.*.moi_quan_he' => 'nullable|string|max:50',
        ]);

        try {
            $phong = DB::table('phong')->where('id', $validated['phong_id'])->first();
            if (!$phong) {
                throw new \Exception('Phòng không tồn tại.');
            }
            if ($phong->trang_thai !== 'trong') {
                throw new \Exception('Phòng này hiện không còn trống, vui lòng chọn phòng khác.');
            }
            $payload = $validated;

            if ($request->filled('nguoi_than')) {
                $payload['nguoi_than'] = json_encode(array_values($request->input('nguoi_than')));
            }

            $files = [];
            if ($request->hasFile('file_hop_dong') && $request->file('file_hop_dong')->isValid()) {
                $files['file_hop_dong'] = $request->file('file_hop_dong');
            }

            $payload = $validated;
            if (isset($payload['nguoi_than'])) {
                $payload['nguoi_than'] = json_encode($payload['nguoi_than']);
            }

            $response = $this->api->postMultipart(
                '/chu-tro/hop-dong',
                $payload,
                ['file_hop_dong' => $request->file('file_hop_dong')]
            );


            if (isset($response['error'])) {
                throw new \Exception($response['error']);
            }

            if ($request->filled('yeu_cau_thue_id')) {
                DB::table('yeu_cau_thue')
                    ->where('id', $request->yeu_cau_thue_id)
                    ->update(['trang_thai' => 'da_tao_hop_dong']);
            }

            return redirect()->route('chu-tro.hop-dong.index')->with('ok', 'Đã tạo hợp đồng thành công!');
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi tạo hợp đồng: ' . $e->getMessage());
            return back()->with('error', 'Không thể tạo hợp đồng: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $this->api->setToken(session('api_token'));

        try {
            $response = $this->api->get("/chu-tro/hop-dong/{$id}");
            $hop_dong = (array) $response;
        } catch (\Throwable $e) {
            return back()->with('error', 'Không thể tải hợp đồng: ' . $e->getMessage());
        }

        return view('chutro.hopdong.show', compact('hop_dong'));
    }


    public function edit($id)
    {
        $this->api->setToken(session('api_token'));

        try {
            $hop_dong = $this->api->get("/chu-tro/hop-dong/{$id}");
            if (!$hop_dong || isset($hop_dong['error'])) {
                return back()->with('error', 'Không tìm thấy hợp đồng hoặc lỗi tải dữ liệu.');
            }
        } catch (\Throwable $e) {
            Log::error('Lỗi khi tải hợp đồng: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu hợp đồng.');
        }

        $chu_tro_id = session('user')['id'];
        $phongs = DB::table('phong')
            ->join('day_tro', 'day_tro.id', '=', 'phong.day_tro_id')
            ->where('day_tro.chu_tro_id', $chu_tro_id)
            ->select('phong.id', 'phong.so_phong', 'day_tro.ten_day_tro')
            ->get();

        $khach_thues = DB::table('khach_thue')
            ->join('nguoi_dung', 'nguoi_dung.id', '=', 'khach_thue.nguoi_dung_id')
            ->select('khach_thue.id', 'nguoi_dung.ho_ten')
            ->get();

        return view('chutro.hopdong.edit', compact('hop_dong', 'phongs', 'khach_thues'));
    }

    public function update(Request $request, $id)
    {
        $this->api->setToken(session('api_token'));

        $validated = $request->validate([
            'phong_id' => 'required|integer',
            'khach_thue_id' => 'required|integer',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'tien_coc' => 'required|numeric|min:0',
            'file_hop_dong' => 'nullable|file|mimes:pdf|max:4096',
        ]);

        try {
            $file = $request->file('file_hop_dong');
            $files = [];

            if ($file && $file->isValid()) {
                $files['file_hop_dong'] = $file;
            }

            $response = $this->api->postMultipart("/chu-tro/hop-dong/{$id}?_method=PUT", $validated, $files);

            if (isset($response['error'])) {
                throw new \Exception($response['error']);
            }

            return redirect()->route('chu-tro.hop-dong.index')->with('ok', 'Cập nhật hợp đồng thành công!');
        } catch (\Throwable $e) {
            Log::error('🟥 Lỗi cập nhật hợp đồng: ' . $e->getMessage());
            return back()->with('error', 'Không thể cập nhật hợp đồng: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $this->api->setToken(session('api_token'));
        try {
            $this->api->delete("/chu-tro/hop-dong/{$id}");
            return back()->with('ok', 'Đã xóa hợp đồng.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Không thể xóa: ' . $e->getMessage());
        }
    }
}
