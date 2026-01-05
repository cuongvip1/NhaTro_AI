<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhongController extends Controller
{

   public function index(Request $r)
{
    $chuTroId = session('user')['id'] ?? null;

    // ===== Query gốc =====
    $query = DB::table('phong')
        ->join('day_tro', 'day_tro.id', '=', 'phong.day_tro_id')
        ->where('day_tro.chu_tro_id', $chuTroId)
        ->select(
            'phong.id',
            'phong.so_phong',
            'phong.gia',
            'phong.trang_thai',
            'phong.suc_chua',
            'phong.dien_tich',
            'phong.tang',
            'day_tro.ten_day_tro',
            'phong.ngay_tao',
            'phong.ngay_cap_nhat'
        );

    // ===== FILTER =====

    // 🔹 Lọc theo dãy trọ
    if ($r->filled('day_tro_id')) {
        $query->where('phong.day_tro_id', $r->day_tro_id);
    }

    // 🔹 Lọc theo trạng thái
    if ($r->filled('trang_thai')) {
        $query->where('phong.trang_thai', $r->trang_thai);
    }


    // ===== Sắp xếp =====
    $phongs = $query
        ->orderBy('day_tro.ten_day_tro')
        ->orderBy('phong.so_phong')
        ->get();

    // Danh sách dãy trọ cho dropdown filter
    $dayTros = DB::table('day_tro')
        ->where('chu_tro_id', $chuTroId)
        ->select('id', 'ten_day_tro')
        ->orderBy('ten_day_tro')
        ->get();

    return view('chutro.phong.index', compact('phongs', 'dayTros'));
}


    public function create()
    {
        $chuTroId = session('user')['id'] ?? null;

        $dayTros = DB::table('day_tro')
            ->where('chu_tro_id', $chuTroId)
            ->select('id', 'ten_day_tro')
            ->orderBy('ten_day_tro')
            ->get();

        return view('chutro.phong.create', compact('dayTros'));
    }

    /*public function store(Request $r)
    {
        $chuTroId = session('user')['id'] ?? null;

        // 1️⃣ Validate dữ liệu đầu vào
        $data = $r->validate([
            'day_tro_id' => 'required|integer|exists:day_tro,id',
            'so_phong' => 'required|string|max:50',
            'gia' => 'required|numeric|min:0',
            'trang_thai' => 'nullable|in:trong,da_thue,bao_tri',
            'suc_chua' => 'required|integer|min:1',
            'dien_tich' => 'required|numeric|min:1',
            'tang' => 'required|integer|min:0',
        ]);

        // 2️⃣ Gán giá trị mặc định
        $data['trang_thai'] = $data['trang_thai'] ?? 'trong';
        $data['ngay_tao'] = now();
        $data['ngay_cap_nhat'] = now();

        // 3️⃣ Lưu phòng mới và lấy ID
        $phongId = DB::table('phong')->insertGetId($data);

        // 4️⃣ Tạo đồng hồ điện & nước tự động
        $dongHoDienId = DB::table('dong_ho')->insertGetId([
            'phong_id' => $phongId,
            'dich_vu_id' => 1, // 1 = Điện
            'ma_dong_ho' => 'DH_Dien_' . $data['so_phong'],
        ]);

        $dongHoNuocId = DB::table('dong_ho')->insertGetId([
            'phong_id' => $phongId,
            'dich_vu_id' => 2, // 2 = Nước
            'ma_dong_ho' => 'DH_Nuoc_' . $data['so_phong'],
        ]);

        // 5️⃣ Ghi chỉ số đầu = 0 cho cả hai đồng hồ
        DB::table('chi_so')->insert([
            [
                'dong_ho_id' => $dongHoDienId,
                'thoi_gian' => now(),
                'gia_tri' => 0,
                'ghi_chu' => 'Chỉ số đầu',
                'nguoi_nhap_id' => $chuTroId,
                'ngay_tao' => now(),
            ],
            [
                'dong_ho_id' => $dongHoNuocId,
                'thoi_gian' => now(),
                'gia_tri' => 0,
                'ghi_chu' => 'Chỉ số đầu',
                'nguoi_nhap_id' => $chuTroId,
                'ngay_tao' => now(),
            ],
        ]);

        // 6️⃣ Thông báo thành công
        return redirect()->route('chu-tro.phong.index')
            ->with('success', '✅ Thêm phòng thành công, đã tự tạo đồng hồ điện & nước!');
    }*/
            public function store(Request $r)
{
    $chuTroId = session('user')['id'] ?? null;

    if (!$chuTroId) {
        return back()->with('error', 'Phiên đăng nhập không hợp lệ.');
    }

    // 1️⃣ Validate
    $data = $r->validate([
        'day_tro_id' => 'required|integer|exists:day_tro,id',
        'so_phong'   => 'required|string|max:50',
        'gia'        => 'required|numeric|min:0',
        'suc_chua'   => 'required|integer|min:1',
        'dien_tich'  => 'required|numeric|min:1',
        'tang'       => 'required|integer|min:0',
    ]);

    // 2️⃣ Check dãy trọ thuộc chủ trọ
    $isOwn = DB::table('day_tro')
        ->where('id', $data['day_tro_id'])
        ->where('chu_tro_id', $chuTroId)
        ->exists();

    if (!$isOwn) {
        return back()->with('error', 'Bạn không sở hữu dãy trọ này.');
    }

    // 3️⃣ Check trùng phòng
    $existsPhong = DB::table('phong')
        ->where('day_tro_id', $data['day_tro_id'])
        ->where('so_phong', $data['so_phong'])
        ->exists();

    if ($existsPhong) {
        return back()->with('error', 'Phòng này đã tồn tại trong dãy trọ.');
    }

    DB::beginTransaction();

    try {
        // 4️⃣ Tạo phòng
        $phongId = DB::table('phong')->insertGetId([
            'day_tro_id'    => $data['day_tro_id'],
            'so_phong'      => $data['so_phong'],
            'gia'           => $data['gia'],
            'suc_chua'      => $data['suc_chua'],
            'dien_tich'     => $data['dien_tich'],
            'tang'          => $data['tang'],
            'trang_thai'    => 'trong',
            'ngay_tao'      => now(),
            'ngay_cap_nhat' => now(),
        ]);

        // 5️⃣ Lấy ID dịch vụ điện & nước (KHÔNG HARDCODE)
        $dvDien = DB::table('dich_vu')->where('ten', 'Điện')->first();
        $dvNuoc = DB::table('dich_vu')->where('ten', 'Nước')->first();

        if (!$dvDien || !$dvNuoc) {
            throw new \Exception('Thiếu dịch vụ Điện hoặc Nước.');
        }

        // 6️⃣ Tạo đồng hồ
        $dongHoDienId = DB::table('dong_ho')->insertGetId([
            'phong_id'   => $phongId,
            'dich_vu_id' => $dvDien->id,
            'ma_dong_ho' => 'DH_DIEN_' . $phongId,
        ]);

        $dongHoNuocId = DB::table('dong_ho')->insertGetId([
            'phong_id'   => $phongId,
            'dich_vu_id' => $dvNuoc->id,
            'ma_dong_ho' => 'DH_NUOC_' . $phongId,
        ]);

        // 7️⃣ Ghi chỉ số đầu
        DB::table('chi_so')->insert([
            [
                'dong_ho_id'   => $dongHoDienId,
                'thoi_gian'    => now(),
                'gia_tri'      => 0,
                'ghi_chu'      => 'Chỉ số đầu',
                'nguoi_nhap_id'=> $chuTroId,
                'ngay_tao'     => now(),
            ],
            [
                'dong_ho_id'   => $dongHoNuocId,
                'thoi_gian'    => now(),
                'gia_tri'      => 0,
                'ghi_chu'      => 'Chỉ số đầu',
                'nguoi_nhap_id'=> $chuTroId,
                'ngay_tao'     => now(),
            ],
        ]);

        DB::commit();

        return redirect()
            ->route('chu-tro.phong.index')
            ->with('success', '✅ Thêm phòng thành công (đã tạo đồng hồ điện & nước).');

    } catch (\Throwable $e) {
        DB::rollBack();

        return back()->with(
            'error',
            '❌ Lỗi khi tạo phòng: ' . $e->getMessage()
        );
    }
}



    public function show($id)
    {
        $phong = DB::table('phong')
            ->join('day_tro', 'day_tro.id', '=', 'phong.day_tro_id')
            ->select(
                'phong.*',
                'day_tro.ten_day_tro',
                'day_tro.dia_chi'
            )
            ->where('phong.id', $id)
            ->first();

        if (!$phong) {
            abort(404, 'Không tìm thấy phòng.');
        }

        $tienIch = DB::table('phong_tien_ich')
            ->join('tien_ich', 'tien_ich.id', '=', 'phong_tien_ich.tien_ich_id')
            ->where('phong_tien_ich.phong_id', $id)
            ->pluck('tien_ich.ten');

        return view('chutro.phong.show', compact('phong', 'tienIch'));
    }

    public function edit($id)
    {
        $chuTroId = session('user')['id'] ?? null;

        $dayTros = DB::table('day_tro')
            ->where('chu_tro_id', $chuTroId)
            ->select('id', 'ten_day_tro')
            ->get();

        $phong = DB::table('phong')->find($id);

        if (!$phong) {
            abort(404, 'Không tìm thấy phòng.');
        }

        return view('chutro.phong.edit', compact('phong', 'dayTros'));
    }

    public function update(Request $r, $id)
    {
        $data = $r->validate([
            'day_tro_id' => 'required|integer|exists:day_tro,id',
            'so_phong' => 'required|string|max:50',
            'gia' => 'required|numeric|min:0',
            'trang_thai' => 'nullable|in:trong,da_thue,bao_tri',
            'suc_chua' => 'required|integer|min:1',
            'dien_tich' => 'required|numeric|min:1',
            'tang' => 'required|integer|min:0',
        ]);

        $data['ngay_cap_nhat'] = Carbon::now();

        DB::table('phong')->where('id', $id)->update($data);

        return redirect()->route('chu-tro.phong.index')
            ->with('success', '✅ Cập nhật thông tin phòng thành công!');
    }

    public function destroy($id)
    {
        $hasContract = DB::table('hop_dong')->where('phong_id', $id)->exists();

        if ($hasContract) {
            return back()->with('error', 'Phòng đã có hợp đồng, không thể xóa.');
        }

        DB::table('phong')->where('id', $id)->delete();

        return redirect()->route('chu-tro.phong.index')
            ->with('success', '🗑️ Đã xóa phòng thành công!');
    }
}
