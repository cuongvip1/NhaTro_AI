<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhachThueController extends Controller
{
    private ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    /** 🔹 Danh sách khách thuê */
    public function index(Request $request)
    {
        $this->api->setToken(session('api_token'));
        $chu_tro_id = session('user')['id'] ?? null;

        $day_tros = DB::table('day_tro')
            ->where('chu_tro_id', $chu_tro_id)
            ->select('id', 'ten_day_tro')
            ->get();

        $khach = $this->api->get('/chu-tro/khach-thue', [
            'day_tro_id' => $request->input('day_tro_id')
        ]) ?? [];

        return view('chutro.khachthue.index', compact('khach', 'day_tros'));
    }

    /** 🔹 Xem chi tiết khách thuê */
    public function show($id)
    {
        $this->api->setToken(session('api_token'));

        $data = $this->api->get("/chu-tro/khach-thue/{$id}");
        if (isset($data['error'])) {
            return back()->with('error', $data['error']);
        }

        return view('chutro.khachthue.show', $data);
    }

    /** 🔹 Form tạo khách thuê */
    public function create()
    {
        return view('chutro.khachthue.create');
    }

    /** 🔹 Lưu khách thuê mới */
    public function store(Request $request)
    {
        $this->api->setToken(session('api_token'));

        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'sdt' => 'nullable|string|max:30',
            'cccd' => 'nullable|string|max:30',
            'ghi_chu' => 'nullable|string|max:1000',
        ]);

        $res = $this->api->post('/chu-tro/khach-thue', $validated);

        if (isset($res['error'])) {
            return back()->with('error', $res['error'] ?? 'Không thể tạo khách thuê')->withInput();
        }

        return redirect()->route('chu-tro.khachthue.index')->with('ok', 'Đã tạo khách thuê thành công!');
    }

    /** 🔹 Form chỉnh sửa khách thuê */
    public function edit($id)
    {
        $this->api->setToken(session('api_token'));

        $data = $this->api->get("/chu-tro/khach-thue/{$id}");
        if (isset($data['error'])) {
            return back()->with('error', $data['error']);
        }

        return view('chutro.khachthue.edit', $data);
    }

    /** 🔹 Cập nhật khách thuê */
    public function update(Request $request, $id)
    {
        $this->api->setToken(session('api_token'));

        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'sdt' => 'nullable|string|max:30',
            'cccd' => 'nullable|string|max:30',
            'ghi_chu' => 'nullable|string|max:1000',
        ]);

        $res = $this->api->put("/chu-tro/khach-thue/{$id}", $validated);

        if (isset($res['error'])) {
            return back()->with('error', $res['error'])->withInput();
        }

        return redirect()->route('chu-tro.khachthue.index')->with('ok', 'Cập nhật khách thuê thành công!');
    }

    /** 🔹 Xóa khách thuê */
    public function destroy($id)
    {
        $this->api->setToken(session('api_token'));

        $res = $this->api->delete("/chu-tro/khach-thue/{$id}");

        if (isset($res['error'])) {
            return back()->with('error', $res['error']);
        }

        return back()->with('ok', 'Đã xóa khách thuê.');
    }
}
