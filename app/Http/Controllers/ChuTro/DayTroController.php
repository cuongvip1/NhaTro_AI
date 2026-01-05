<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use App\Models\DayTro;
use App\Models\DiaChi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DayTroController extends Controller
{
    public function index(Request $request)
    {
        $chuTroId = session('user')['id'] ?? null;

        $q = DayTro::where('chu_tro_id', $chuTroId)
            ->withCount('phong')                  // phong_count
            ->withAvg('phong', 'dien_tich')       // phong_avg_dien_tich
            ->withAvg('phong', 'gia');            // phong_avg_gia

        if ($search = $request->q) {
            $q->where(function ($sub) use ($search) {
                $sub->where('ten_day_tro', 'like', "%{$search}%")
                    ->orWhere('dia_chi', 'like', "%{$search}%");
            });
        }

        $dayTroList = $q->orderByDesc('ngay_tao')->get()->map(function ($item) {
            // 🔁 Chỉ bổ sung nếu cột đang NULL
            if ($item->so_phong === null) {
                $item->so_phong = (int) ($item->phong_count ?? 0);
            }
            if ($item->dien_tich_tb === null) {
                $item->dien_tich_tb = $item->phong_avg_dien_tich
                    ? round($item->phong_avg_dien_tich, 1)
                    : null;
            }
            if ($item->gia_trung_binh === null) {
                $item->gia_trung_binh = $item->phong_avg_gia
                    ? round($item->phong_avg_gia, 0)
                    : null;
            }
            return $item;
        });

        return view('chutro.daytro.index', compact('dayTroList'));
    }

    public function create()
    {
        // Load available regions (khu vực) from `dia_chi` table so the create form
        // can present a select input for the address.
        $regions = DiaChi::orderBy('ten_dia_chi')->get();
        return view('chutro.daytro.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_day_tro' => 'required|string|max:255',
            'dia_chi' => 'required|string|max:255',
            'so_phong' => 'nullable|integer|min:0',
            'dien_tich_tb' => 'nullable|numeric|min:0',
            'gia_trung_binh' => 'nullable|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'tien_ich' => 'nullable|string',
        ]);

        DayTro::create([
            'chu_tro_id' => session('user.id'),
            'ten_day_tro' => $request->ten_day_tro,
            'dia_chi' => $request->dia_chi,
            'so_phong' => $request->so_phong,
            'dien_tich_tb' => $request->dien_tich_tb,
            'gia_trung_binh' => $request->gia_trung_binh,
            'mo_ta' => $request->mo_ta,
            'tien_ich' => $request->tien_ich,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        return redirect()
            ->route('chu-tro.day-tro.index')
            ->with('success', '✅ Thêm dãy trọ thành công!');
    }


    public function edit($id)
    {
        $dayTro = DayTro::findOrFail($id);
        return view('chutro.daytro.edit', compact('dayTro'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_day_tro' => 'required|string|max:255',
            'dia_chi' => 'required|string|max:255',
            'so_phong' => 'nullable|integer|min:0',
            'dien_tich_tb' => 'nullable|numeric|min:0',
            'gia_trung_binh' => 'nullable|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'tien_ich' => 'nullable|string',
        ]);

        $dayTro = DayTro::findOrFail($id);

        $dayTro->update([
            'ten_day_tro' => $request->ten_day_tro,
            'dia_chi' => $request->dia_chi,
            'so_phong' => $request->so_phong,
            'dien_tich_tb' => $request->dien_tich_tb,
            'gia_trung_binh' => $request->gia_trung_binh,
            'mo_ta' => $request->mo_ta,
            'tien_ich' => $request->tien_ich,
            'ngay_cap_nhat' => now(),
        ]);

        return redirect()
            ->route('chu-tro.day-tro.index')
            ->with('success', '✅ Cập nhật dãy trọ thành công!');
    }


    public function destroy($id)
    {
        DayTro::destroy($id);
        return redirect()
            ->route('chu-tro.day-tro.index')
            ->with('success', '🗑️ Xóa dãy trọ thành công!');
    }

    public function show($id)
    {
        $chuTroId = session('user')['id'] ?? null;

        $dayTro = \App\Models\DayTro::with([
            'phong' => function ($q) {
                $q->select('id', 'day_tro_id', 'so_phong', 'dien_tich', 'gia', 'trang_thai');
            }
        ])
            ->where('chu_tro_id', $chuTroId)
            ->where('id', $id)
            ->firstOrFail();

        return view('chutro.daytro.show', compact('dayTro'));
    }

}
