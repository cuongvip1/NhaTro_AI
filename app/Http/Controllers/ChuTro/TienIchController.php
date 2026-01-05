<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TienIch;
use App\Models\Phong;
use Illuminate\Support\Facades\Auth;

class TienIchController extends Controller
{
    // ✅ Hiển thị danh sách tiện ích
    public function index()
    {
        $chu_tro = Auth::guard('web')->user();
        if (!$chu_tro) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }

        $tien_ichs = TienIch::with([
            'phongs' => function ($q) use ($chu_tro) {
                $q->whereHas('dayTro', fn($d) => $d->where('chu_tro_id', $chu_tro->id));
            }
        ])->get();

        return view('chutro.tienich.index', compact('tien_ichs'));
    }


    // ✅ Lưu tiện ích mới
    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
        ]);

        TienIch::create(['ten' => $request->ten]);

        return redirect()->back()->with('success', 'Thêm tiện ích thành công!');
    }

    // ✅ Xóa tiện ích
    public function destroy($id)
    {
        $tien_ich = TienIch::findOrFail($id);
        $tien_ich->delete();

        return redirect()->back()->with('success', 'Đã xóa tiện ích!');
    }

    // ✅ Hiển thị tiện ích của từng phòng
    public function phong($phong_id)
    {
        $chu_tro = Auth::guard('web')->user();

        $phong = Phong::with('tienIch')
            ->whereHas('dayTro', fn($q) => $q->where('chu_tro_id', $chu_tro->id))
            ->findOrFail($phong_id);

        $tatCaTienIch = TienIch::all();

        return view('chutro.tienich.phong', compact('phong', 'tatCaTienIch'));
    }

    // ✅ Gán tiện ích cho phòng
    public function ganTienIch(Request $request, $phong_id)
    {
        $chu_tro = Auth::guard('web')->user();

        $phong = Phong::whereHas('dayTro', fn($q) => $q->where('chu_tro_id', $chu_tro->id))
            ->findOrFail($phong_id);

        $phong->tienIch()->sync($request->tien_ich_ids ?? []);

        return redirect()->back()->with('success', 'Cập nhật tiện ích cho phòng thành công!');
    }
}
