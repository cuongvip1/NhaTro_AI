<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->with('error', 'Bạn cần đăng nhập để xem thông báo.');
        }

        $thong_bao = ThongBao::where('nguoi_nhan_id', $user->id)
            ->orderByDesc('ngay_tao')
            ->get();
        $thong_bao_chua_xem = ThongBao::where('nguoi_nhan_id', $user->id)
            ->where('da_xem', 0)
            ->count();

        return view('chutro.thongbao.index', compact('thong_bao', 'thong_bao_chua_xem'));
    }

    public function daXem($id)
    {
        $tb = ThongBao::findOrFail($id);
        $tb->update(['da_xem' => 1]);
        return redirect($tb->lien_ket ?? '/chu-tro/dashboard');
    }
    public function markAsRead($id)
    {
        $tb = ThongBao::where('id', $id)
            ->where('nguoi_nhan_id', Auth::id())
            ->first();

        if ($tb) {
            $tb->update(['da_xem' => 1]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
    public function markAllRead()
    {
        try {
            ThongBao::where('nguoi_nhan_id', auth()->id())
                ->update(['da_xem' => 1]);

            return response()->json(['success' => true, 'message' => 'Đã đánh dấu tất cả thông báo là đã đọc.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function xoaDaDoc()
    {
        try {
            $userId = auth()->id();

            // 🔥 Xóa toàn bộ thông báo đã đọc của chủ trọ hiện tại
            $count = ThongBao::where('nguoi_nhan_id', $userId)
                ->where('da_xem', 1)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa $count thông báo đã đọc."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa thông báo: ' . $e->getMessage()
            ], 500);
        }
    }

}
