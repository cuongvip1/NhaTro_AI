<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BaiDang;
use App\Models\Phong;
use App\Models\DichVu;
use App\Models\DiaChi;

class BaiDangWebController extends Controller
{
    private function getToken()
    {
        $user = session('user');
        return session('api_token') ?? ($user['api_token'] ?? null);
    }

      /* public function index(Request $request)
    {
        $user = session('user');

        $search = $request->get('search', '');
        $status = $request->get('status'); // null | 'dang' | 'cho_duyet' | 'an'

        $query = BaiDang::with([
            'anh' => fn($q) => $q->orderBy('thu_tu'),
            'phong.dayTro',
            'phong.dichVuDinhKy.dichVu'
        ])->whereHas('phong.dayTro', function ($q) use ($user) {
            $q->where('chu_tro_id', $user['id']);
        });

        // ✅ CHỈ lọc khi user chọn
        if ($status !== null && $status !== '') {
            $query->where('trang_thai', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                ->orWhere('mo_ta', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderByDesc('id')->paginate(9);

        return view('chutro.baidang.index', compact('posts', 'status'));
    }*/   
public function index(Request $request)
{
    $user = session('user');

    $search = $request->get('search', '');
    $status = $request->get('status'); // chỉ lọc khi user chọn

    $query = BaiDang::with([
        'anh' => fn($q) => $q->orderBy('thu_tu'),
        'phong.dayTro',
        'phong.dichVuDinhKy.dichVu'
    ])->whereHas('phong.dayTro', function ($q) use ($user) {
        $q->where('chu_tro_id', $user['id']);
    });

    // ✅ CHỈ LỌC KHI USER CHỌN
    if ($request->filled('status')) {
        $query->where('trang_thai', $status);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('tieu_de', 'like', "%{$search}%")
              ->orWhere('mo_ta', 'like', "%{$search}%");
        });
    }

    $posts = $query->orderByDesc('id')->paginate(9);

    return view('chutro.baidang.index', compact('posts', 'status'));
}


    /** ➕ Form tạo bài đăng */
    public function create()
    {
        $user = session('user');

        // Lấy danh sách phòng thuộc chủ trọ
        $phongList = Phong::whereHas('dayTro', callback: function ($q) use ($user) {
            $q->where('chu_tro_id', $user['id']);
        })
            ->where('trang_thai', 'trong')
            ->whereDoesntHave('baiDang', function ($q) {
    $q->where('trang_thai', 'dang');
})

            ->with('dayTro')
            ->get();


        $tienIchList = DichVu::all();
        $regions = DiaChi::orderBy('ten_dia_chi')->get();

        return view('chutro.baidang.create', compact('phongList', 'tienIchList', 'regions'));
    }

    /** 💾 Lưu bài đăng mới */
    public function store(Request $request)
    {

        try {
            $token = session('api_token') ?? (session('user')['api_token'] ?? null);

            if (!$token) {
                return back()
                    ->withInput()
                    ->with('error', '⚠️ Phiên đăng nhập API đã hết hạn. Vui lòng đăng nhập lại.');
            }

            $request->validate([
                'phong_id' => 'required|exists:phong,id',
                'tieu_de' => 'required|string|max:255',
                'mo_ta' => 'required|string',
                'gia_niem_yet' => 'required|numeric|min:0',
                'dia_chi' => 'nullable|string|max:255',
                'anh' => 'required',
            ]);

            $apiUrl = "http://127.0.0.1:8000/api/chu-tro/bai-dang";

            $multipart = [];

            $diaChiInput = trim((string) $request->dia_chi);
            if ($diaChiInput === '') {
                $phong = Phong::with('dayTro:id,dia_chi')->find($request->phong_id);
                if ($phong && $phong->dayTro && !empty($phong->dayTro->dia_chi)) {
                    $diaChiInput = $phong->dayTro->dia_chi;
                }
            }
            if ($diaChiInput === '') {
                $diaChiInput = null;
            }

            $fields = [
                'phong_id' => $request->phong_id,
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'gia_niem_yet' => $request->gia_niem_yet,
                'dia_chi' => $diaChiInput,
            ];

            foreach ($fields as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value ?? '',
                ];
            }

            if ($request->has('tien_ich')) {
                foreach ($request->tien_ich as $dv) {
                    $multipart[] = [
                        'name' => 'tien_ich[]',
                        'contents' => $dv,
                    ];
                }
            }

            if ($request->has('tien_ich_moi')) {
                foreach ($request->tien_ich_moi as $dvMoi) {
                    $multipart[] = [
                        'name' => 'tien_ich_moi[]',
                        'contents' => $dvMoi,
                    ];
                }
            }

            if ($request->hasFile('anh')) {
                foreach ($request->file('anh') as $file) {
                    $multipart[] = [
                        'name' => 'anh[]',
                        'contents' => fopen($file->getRealPath(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ];
                }
            }

            $response = Http::withToken($token)
                ->asMultipart()
                ->timeout(60)
                ->post($apiUrl, $multipart);

            if ($response->successful()) {
                return redirect()
    ->route('chu-tro.bai-dang.index', ['status' => 'cho_duyet'])
    ->with('success', '🕓 Bài đăng đã tạo và đang chờ admin duyệt');

            }

            $msg = $response->json('error') ?? $response->json('message') ?? 'Không xác định.';
            return back()->withInput()->with('error', '❌ Lỗi API: ' . $msg);

        } catch (\Throwable $e) {
            Log::error('❌ Lỗi tạo bài đăng Web: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', '⚠️ Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /** Chi tiết bài đăng */
    public function show($id)
    {
        $user = session('user');
        $post = BaiDang::with(['anh', 'phong.dayTro', 'phong.dichVuDinhKy.dichVu'])
            ->whereHas('phong.dayTro', fn($q) => $q->where('chu_tro_id', $user['id']))
            ->findOrFail($id);

        return view('chutro.baidang.show', compact('post'));
    }

    /**  chỉnh sửa */
    public function edit($id)
    {
        $user = session('user');
        $post = BaiDang::with(['anh', 'phong.dayTro', 'phong.dichVuDinhKy'])
            ->whereHas('phong.dayTro', fn($q) => $q->where('chu_tro_id', $user['id']))
            ->findOrFail($id);

        $phongList = Phong::join('day_tro as d', 'd.id', '=', 'phong.day_tro_id')
            ->where('d.chu_tro_id', $user['id'])
            ->select('phong.id', DB::raw("CONCAT(d.ten_day_tro, ' - Phòng ', phong.so_phong) as ten"))
            ->get();

        $tienIchList = DichVu::all();
        $regions = DiaChi::orderBy('ten_dia_chi')->get();

        return view('chutro.baidang.edit', compact('post', 'phongList', 'tienIchList', 'regions'));
    }

    /** 💾 Cập nhật bài đăng */
    public function update(Request $request, $id)
    {
        try {
            $token = $this->getToken();

            if (!$token) {
                return back()->withErrors(['error' => '⚠️ Chưa đăng nhập API. Vui lòng đăng nhập lại.']);
            }

            $apiUrl = "http://127.0.0.1:8000/api/chu-tro/bai-dang/{$id}";

            $multipart = [];

            $fields = [
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'gia_niem_yet' => $request->gia_niem_yet,
                'dia_chi' => $request->dia_chi,
            ];

            foreach ($fields as $key => $val) {
                $multipart[] = ['name' => $key, 'contents' => $val ?? ''];
            }

            if ($request->has('xoa_anh_cu')) {
                foreach ($request->xoa_anh_cu as $idAnh) {
                    $multipart[] = ['name' => 'xoa_anh_cu[]', 'contents' => $idAnh];
                }
            }

            if ($request->has('tien_ich')) {
                foreach ($request->tien_ich as $dvId) {
                    $multipart[] = ['name' => 'tien_ich[]', 'contents' => $dvId];
                }
            }

            if ($request->hasFile('anh')) {
                foreach ($request->file('anh') as $file) {
                    $multipart[] = [
                        'name' => 'anh[]',
                        'contents' => fopen($file->getRealPath(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ];
                }
            }

            $multipart[] = ['name' => '_method', 'contents' => 'PUT'];

            $response = Http::withToken($token)
                ->asMultipart()
                ->post($apiUrl, $multipart);

            if ($response->successful()) {
                return redirect()
                    ->route('chu-tro.bai-dang.show', $id)
                    ->with('success', '✅ Cập nhật bài đăng thành công!');
            }

            $errorMsg = $response->json('error') ?? $response->body();
            return back()->withErrors(['error' => '❌ Lỗi API: ' . $errorMsg]);

        } catch (\Throwable $e) {
            Log::error('❌ Lỗi update bài đăng web: ' . $e->getMessage());
            return back()->withErrors(['error' => '⚠️ Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    /**  Xóa bài đăng */
    public function destroy($id)
    {
        try {
            $token = $this->getToken();

            if (!$token) {
                return back()->withErrors(['error' => '⚠️ Chưa đăng nhập API. Vui lòng đăng nhập lại.']);
            }

            $apiUrl = "http://127.0.0.1:8000/api/chu-tro/bai-dang/{$id}";
            $response = Http::withToken($token)->delete($apiUrl);

            if ($response->successful()) {
                return redirect()->route('chu-tro.bai-dang.index')
                    ->with('success', '🗑️ Xóa bài đăng thành công!');
            }

            return back()->withErrors(['error' => 'Lỗi API: ' . $response->body()]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }
    /** Bật/tắt hiển thị bài đăng (ẩn / hiện) */
    public function toggle($id)
    {
        try {
            $token = $this->getToken();

            if (!$token) {
                return back()->withErrors(['error' => '⚠️ Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.']);
            }
            $apiUrl = "http://127.0.0.1:8000/api/chu-tro/bai-dang/{$id}/toggle";

            $response = \Illuminate\Support\Facades\Http::withToken($token)->post($apiUrl);

            if ($response->successful()) {
                return back()->with('success', $response->json('message') ?? '✅ Đã cập nhật trạng thái bài đăng.');
            }

            $error = $response->json('error') ?? $response->body();
            return back()->withErrors(['error' => "❌ Không thể thay đổi trạng thái bài đăng: {$error}"]);

        } catch (\Throwable $e) {
            Log::error('❌ Lỗi toggle bài đăng Web: ' . $e->getMessage());
            return back()->withErrors(['error' => '⚠️ Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }
}
