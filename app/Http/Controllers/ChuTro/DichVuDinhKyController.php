<?php
namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DichVuDinhKyController extends Controller
{
    public function index()
    {
        $chu_tro_id = session('user')['id'];

        $phongs = DB::table('phong')
            ->join('day_tro', 'day_tro.id', '=', 'phong.day_tro_id')
            ->where('day_tro.chu_tro_id', $chu_tro_id)
            ->select('phong.id', 'phong.so_phong', 'day_tro.ten_day_tro')
            ->get();

        return view('chutro.dichvu_dinhky.index', compact('phongs'));
    }

    public function show($phong_id)
    {
        $dich_vus = DB::table('dich_vu_dinh_ky')
            ->join('dich_vu', 'dich_vu.id', '=', 'dich_vu_dinh_ky.dich_vu_id')
            ->where('phong_id', $phong_id)
            ->select('dich_vu_dinh_ky.*', 'dich_vu.ten', 'dich_vu.don_vi')
            ->get();

        $tatCaDv = DB::table('dich_vu')->select('id', 'ten')->get();

        return view('chutro.dichvu_dinhky.show', compact('phong_id', 'dich_vus', 'tatCaDv'));
    }

    public function store(Request $request, $phong_id)
    {
        $validated = $request->validate([
            'dich_vu_id' => 'required|integer',
            'don_gia' => 'required|numeric|min:0',
            'so_luong' => 'required|numeric|min:0.1',
        ]);

        DB::table('dich_vu_dinh_ky')->insert([
            'phong_id' => $phong_id,
            'dich_vu_id' => $validated['dich_vu_id'],
            'don_gia' => $validated['don_gia'],
            'so_luong' => $validated['so_luong'],
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        return back()->with('ok', '✅ Đã thêm dịch vụ định kỳ cho phòng!');
    }

    public function destroy($id)
    {
        $deleted = DB::table('dich_vu_dinh_ky')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => '🗑️ Đã xóa dịch vụ khỏi phòng!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dịch vụ.'], 404);
        }
    }

}
