@extends('layouts.chu-tro')
@section('title', 'Chi tiết khách thuê')

@section('content')
    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-8 rounded-xl shadow space-y-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="ri-user-3-line"></i> Thông tin khách thuê
            </h1>
            <a href="{{ route('chu-tro.khachthue.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="ri-arrow-left-line"></i> Quay lại
            </a>
        </div>

        {{-- Thông tin cơ bản --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="font-semibold text-gray-700">Họ tên:</p>
                <p>{{ $khach_thue['ho_ten'] }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-700">Email:</p>
                <p>{{ $khach_thue['email'] ?? '—' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-700">SĐT:</p>
                <p>{{ $khach_thue['so_dien_thoai'] ?? '—' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-700">CCCD:</p>
                <p>{{ $khach_thue['cccd'] ?? '—' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-700">Ngân sách (VNĐ):</p>
                <p>
                    {{ number_format($khach_thue['ngan_sach_min'] ?? 0, 0, ',', '.') }}
                    – {{ number_format($khach_thue['ngan_sach_max'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Lịch sử hợp đồng --}}
        <div class="border-t pt-6">
            <h2 class="text-lg font-semibold mb-3">📄 Lịch sử hợp đồng thuê</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/40">
                        <tr>
                            <th class="p-3 text-left">Phòng</th>
                            <th class="p-3 text-left">Dãy trọ</th>
                            <th class="p-3 text-left">Thời gian</th>
                            <th class="p-3 text-left">Tiền cọc</th>
                            <th class="p-3 text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($hop_dong as $hd)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="p-3">{{ $hd['so_phong'] }}</td>
                                            <td class="p-3">{{ $hd['ten_day_tro'] }}</td>
                                            <td class="p-3">
                                                {{ \Carbon\Carbon::parse($hd['ngay_bat_dau'])->format('d/m/Y') }}
                                                → {{ \Carbon\Carbon::parse($hd['ngay_ket_thuc'])->format('d/m/Y') }}
                                            </td>
                                            <td class="p-3">{{ number_format($hd['tien_coc'], 0, ',', '.') }} đ</td>
                                            <td class="p-3 text-center">
                                                <span class="px-2 py-1 text-xs rounded-full
                                                                            {{ $hd['trang_thai'] == 'hieu_luc'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-700' }}">
                                                    {{ ucfirst($hd['trang_thai']) }}
                                                </span>
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-6 text-gray-500">Chưa có hợp đồng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection