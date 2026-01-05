@extends('layouts.tenant-layout')

@section('title', 'Chi tiết hợp đồng')
@section('page_title', 'Chi tiết hợp đồng thuê')

@section('tenant_content')
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
        {{-- 🧭 Thanh điều hướng --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                🏠 Thông tin hợp đồng
            </h2>
            <a href="{{ route('khach-thue.hop-dong.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-100 rounded-lg transition">
                <i class="ri-arrow-left-line"></i> Quay lại
            </a>
        </div>

        {{-- ⚙️ Thông tin hợp đồng --}}
        <div class="grid md:grid-cols-2 gap-6">
            {{-- Thông tin phòng --}}
            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-indigo-600">Thông tin phòng trọ</h3>
                <p><strong>Phòng:</strong> {{ $hopDong['phong']['so_phong'] ?? '—' }}</p>
                <p><strong>Dãy trọ:</strong> {{ $hopDong['phong']['day_tro']['ten_day_tro'] ?? '—' }}</p>
                <p><strong>Địa chỉ:</strong> {{ $hopDong['phong']['day_tro']['dia_chi'] ?? '—' }}</p>
                <p><strong>Giá thuê:</strong>
                    {{ number_format($hopDong['phong']['gia'] ?? 0, 0, ',', '.') }} đ/tháng
                </p>
            </div>

            {{-- Thông tin hợp đồng --}}
            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-indigo-600">Chi tiết hợp đồng</h3>
                <p><strong>Ngày bắt đầu:</strong>
                    {{ \Carbon\Carbon::parse($hopDong['ngay_bat_dau'])->format('d/m/Y') }}
                </p>
                <p><strong>Ngày kết thúc:</strong>
                    {{ \Carbon\Carbon::parse($hopDong['ngay_ket_thuc'])->format('d/m/Y') }}
                </p>
                <p><strong>Tiền cọc:</strong>
                    {{ number_format($hopDong['tien_coc'] ?? 0, 0, ',', '.') }} đ
                </p>
                <p>
                    <strong>Trạng thái:</strong>
                    @php
                        $status = $hopDong['trang_thai'] ?? 'khong_xac_dinh';
                        $color = match ($status) {
                            'hieu_luc' => 'bg-green-100 text-green-700',
                            'huy' => 'bg-red-100 text-red-600',
                            default => 'bg-gray-200 text-gray-700'
                        };
                    @endphp
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $color }}">
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </span>
                </p>
            </div>
        </div>

        {{-- 🧾 Xem file hợp đồng --}}
        @if (!empty($hopDong['url_file_hop_dong']))
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-indigo-600 mb-3">File hợp đồng</h3>
                <div class="flex items-center gap-4">
                    <a href="{{ $hopDong['url_file_hop_dong'] }}" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg shadow hover:scale-[1.02] transition">
                        <i class="ri-file-pdf-2-line text-lg"></i> Xem hợp đồng PDF
                    </a>
                    <p class="text-gray-500 text-sm">* File sẽ mở trong tab mới</p>
                </div>
            </div>
        @else
            <div class="mt-8 text-gray-500 italic">
                Chưa có file hợp đồng được đính kèm.
            </div>
        @endif

        {{-- 💰 Hóa đơn liên quan --}}
        @if (!empty($hopDong['hoa_don']))
            <div class="mt-10">
                <h3 class="text-lg font-semibold text-indigo-600 mb-4">Các hóa đơn liên quan</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Mã hóa đơn</th>
                                <th class="px-4 py-3 text-left">Tổng tiền</th>
                                <th class="px-4 py-3 text-left">Trạng thái</th>
                                <th class="px-4 py-3 text-left">Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hopDong['hoa_don'] as $hd)
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="px-4 py-2">{{ $hd['ma_hoa_don'] ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ number_format($hd['tong_tien'] ?? 0, 0, ',', '.') }} đ</td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 rounded text-xs font-semibold
                                                                                {{ ($hd['trang_thai'] ?? '') === 'chua_thanh_toan'
                                ? 'bg-yellow-100 text-yellow-700'
                                : 'bg-green-100 text-green-700' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $hd['trang_thai'] ?? '')) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ \Carbon\Carbon::parse($hd['ngay_tao'])->format('d/m/Y') }}
                                            </td>
                                        </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection