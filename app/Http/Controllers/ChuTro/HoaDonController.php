<?php

namespace App\Http\Controllers\ChuTro;

use App\Exports\TienTroChuTroExport;
use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class HoaDonController extends Controller
{
    public function __construct(protected ApiClient $api)
    {
    }

    public function index()
    {
        return view('chutro.hoadon.index');
    }

    public function export(Request $request)
    {
        if (!session('api_token')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để xuất dữ liệu.');
        }

        $this->api->setToken(session('api_token'));

        $query = array_filter([
    'phong' => $request->string('phong')->toString(),
    'thang' => $request->string('thang')->toString(),

    // 👇 BẮT BUỘC: chỉ xuất doanh thu
    'trang_thai' => 'da_thanh_toan',
], fn ($value) => $value !== '' && $value !== null);


        try {
            $response = $this->api->get('/chu-tro/hoa-don', $query);
        } catch (\Throwable $e) {
            return $this->exportFallback("Không thể tải dữ liệu hóa đơn: {$e->getMessage()}");
        }

        $rows = collect($response ?? [])->map(function ($bill) {
            $rent = (float) ($bill['tien_phong'] ?? 0);
            $service = (float) ($bill['tien_dich_vu'] ?? 0);
            $total = (float) ($bill['tong_tien'] ?? ($rent + $service + (float) ($bill['tien_dong_ho'] ?? 0)));

            $tenantName = data_get($bill, 'hop_dong.khach_thue.nguoi_dung.ho_ten')
                ?? data_get($bill, 'hop_dong.khach_thue.ho_ten')
                ?? data_get($bill, 'hop_dong.ten_khach_thue')
                ?? data_get($bill, 'khach_thue.nguoi_dung.ho_ten')
                ?? data_get($bill, 'khach_thue.ho_ten')
                ?? 'Chưa cập nhật';

            return [
                'ten_phong' => data_get($bill, 'hop_dong.phong.so_phong')
                    ?? data_get($bill, 'phong.so_phong')
                    ?? 'Chưa cập nhật',
                'nguoi_o' => $tenantName,
                'tien_tro' => $rent,
                'tien_dich_vu' => $service,
                'tong_tien' => $total,
            ];
        });

        $ownerName = session('user')['ho_ten'] ?? 'chu-tro';
        $normalizedName = Str::of($ownerName)->slug('_');
        $dateSuffix = now(config('app.timezone'))?->format('Y_m_d') ?? now()->format('Y_m_d');
        $fileName = $normalizedName . '_' . $dateSuffix . '.xlsx';
        return Excel::download(new TienTroChuTroExport($rows), $fileName);
    }

    protected function exportFallback(string $message): RedirectResponse
    {
        return redirect()
            ->route('chu-tro.hoa-don.index')
            ->with('error', $message);
    }
}
