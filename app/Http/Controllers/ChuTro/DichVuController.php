<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DichVuController extends Controller
{
    public function index()
    {
        $chu_tro_id = session('user')['id'];

        $dich_vus = DB::table('dich_vu')
            ->where('chu_tro_id', $chu_tro_id)
            ->orderBy('ten')
            ->get();

        return view('chutro.dichvu.index', compact('dich_vus'));
    }

    public function create()
    {
        return view('chutro.dichvu.create');
    }

    public function store(Request $request)
    {
        $chu_tro_id = session('user')['id'];

        $validated = $request->validate([
            'ten' => 'required|string|max:255',
            'don_vi' => 'required|string|max:50',
            'don_gia' => 'required|numeric|min:0',
            'co_dong_ho' => 'boolean',
        ]);

        $lastId = DB::table('dich_vu')->max('id');
        $validated['ma'] = 'DV' . str_pad(($lastId + 1), 3, '0', STR_PAD_LEFT);

        $validated['chu_tro_id'] = $chu_tro_id;

        DB::table('dich_vu')->insert($validated);

        return redirect()->route('chu-tro.dich-vu.index')->with('success', '✅ Thêm dịch vụ thành công!');
    }


    public function edit($id)
    {
        $chu_tro_id = session('user')['id'];

        $dich_vu = DB::table('dich_vu')
            ->where('chu_tro_id', $chu_tro_id)
            ->where('id', $id)
            ->first();

        if (!$dich_vu) {
            return redirect()->route('chutro.dichvu.index')->with('error', 'Không tìm thấy dịch vụ.');
        }

        return view('chutro.dichvu.edit', compact('dich_vu'));
    }

    public function update(Request $request, $id)
    {
        try {
            $chu_tro_id = session('user')['id'];

            $validated = $request->validate([
                'ma' => 'required|string|max:50',
                'ten' => 'required|string|max:255',
                'don_vi' => 'required|string|max:50',
                'don_gia' => 'required|numeric|min:0',
                'co_dong_ho' => 'nullable|boolean',
            ]);

            $validated['co_dong_ho'] = $request->has('co_dong_ho') ? 1 : 0;

            $affected = DB::table('dich_vu')
                ->where('chu_tro_id', $chu_tro_id)
                ->where('id', $id)
                ->update($validated);

            if (!$affected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy dịch vụ hoặc dữ liệu không thay đổi.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Cập nhật dịch vụ thành công!'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Lỗi cập nhật dịch vụ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi! Không thể cập nhật dịch vụ. Vui lòng kiểm tra lại dữ liệu.'
            ], 500);
        }
    }


    public function destroy($id)
    {
        $chu_tro_id = session('user')['id'];

        DB::table('dich_vu')
            ->where('chu_tro_id', $chu_tro_id)
            ->where('id', $id)
            ->delete();

        return redirect()->route('chu-tro.dich-vu.index')->with('success', '🗑️ Xóa dịch vụ thành công!');
    }
}
