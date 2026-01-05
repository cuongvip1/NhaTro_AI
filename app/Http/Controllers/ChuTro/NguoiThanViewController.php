<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NguoiThanViewController extends Controller
{
    protected $apiBase = 'http://localhost:8000/api/chu-tro/nguoi-than';

    // Lấy danh sách người thân của 1 khách thuê cụ thể
    public function index($khachThueId)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiBase}?khach_thue_id={$khachThueId}");

        $nguoiThan = $response->json()['data'] ?? [];

        return view('chutro.nguoithan.index', compact('nguoiThan', 'khachThueId'));
    }

    //Form tạo người thân
    public function create($khachThueId)
    {
        return view('chutro.nguoithan.form', compact('khachThueId'));
    }

    // Thêm người thân
    public function store(Request $request)
    {
        $response = Http::withToken(session('api_token'))
            ->post($this->apiBase, $request->all());

        if ($response->successful()) {
            return redirect()
                ->route('chu-tro.nguoi-than.index', $request->khach_thue_id)
                ->with('success', 'Thêm người thân thành công!');
        }

        return back()->with('error', 'Không thể thêm người thân.');
    }

    // Sửa người thân
    public function edit($id, $khachThueId)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiBase}?khach_thue_id={$khachThueId}");

        $nguoiThan = collect($response->json()['data'] ?? [])->firstWhere('id', (int) $id);

        if (!$nguoiThan) {
            return redirect()->route('chu-tro.nguoi-than.index', $khachThueId)
                ->with('error', 'Không tìm thấy người thân này.');
        }

        return view('chutro.nguoithan.edit', compact('nguoiThan', 'khachThueId'));
    }

    // Cập nhật người thân
    public function update(Request $request, $id, $khachThueId)
    {
        $response = Http::withToken(session('api_token'))
            ->put("{$this->apiBase}/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()
                ->route('chu-tro.nguoi-than.index', $khachThueId)
                ->with('success', 'Cập nhật người thân thành công!');
        }

        return back()->with('error', 'Không thể cập nhật người thân.');
    }

    // Xóa người thân
    public function destroy($id, $khachThueId)
    {
        $response = Http::withToken(session('api_token'))
            ->delete("{$this->apiBase}/{$id}");

        if ($response->successful()) {
            return redirect()
                ->route('chu-tro.nguoi-than.index', $khachThueId)
                ->with('success', 'Đã xóa người thân!');
        }

        return back()->with('error', 'Không thể xóa người thân.');
    }

    public function listAll()
    {
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('login')
                ->with('error', 'Bạn chưa đăng nhập hoặc phiên đăng nhập đã hết hạn.');
        }

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->get("{$this->apiBase}");

            if ($response->failed()) {
                $status = $response->status();
                $errorMessage = $response->json('message') ?? 'Không thể tải danh sách người thân.';

                if ($status === 401) {
                    return redirect()->route('login')
                        ->with('error', 'Phiên đăng nhập hết hạn, vui lòng đăng nhập lại.');
                }

                return back()->with('error', "API lỗi ({$status}): {$errorMessage}");
            }

            $data = $response->json();
            $nguoiThan = collect($data['data'] ?? [])->toArray();

            return view('chutro.nguoithan.all', compact('nguoiThan'));

        } catch (\Throwable $e) {
            return back()->with('error', 'Không thể kết nối đến API: ' . $e->getMessage());
        }
    }


}
