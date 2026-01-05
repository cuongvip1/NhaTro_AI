@extends('layouts.tenant-layout')

@section('title', 'Hóa đơn')
@section('page_title', 'Danh sách hóa đơn của bạn')

@section('tenant_content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i class="ri-bill-line text-indigo-500 text-2xl"></i>
            Hóa đơn thanh toán
        </h2>
    </div>

    @if(empty($hoaDon) || count($hoaDon) === 0)
        <div class="flex flex-col items-center justify-center h-56 text-gray-500 dark:text-gray-400">
            <i class="ri-file-warning-line text-4xl mb-3 text-gray-400"></i>
            <p>Bạn chưa có hóa đơn nào.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($hoaDon as $hd)
                <div
                    class="group bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 relative">
                    
                    {{-- Header --}}
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            🧾 Tháng {{ $hd['thang'] ?? '--' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Phòng: <strong>{{ $hd['hop_dong']['phong']['so_phong'] ?? 'N/A' }}</strong>
                        </p>
                    </div>

                    {{-- Body --}}
                    <div class="p-5 space-y-2 text-gray-700 dark:text-gray-300">
                        <p>💰 <strong>{{ number_format($hd['tong_tien'] ?? 0) }}đ</strong></p>
                        <p>
                            📅 Hạn:
                            {{ !empty($hd['han_thanh_toan'])
                                ? \Carbon\Carbon::parse($hd['han_thanh_toan'])->format('d/m/Y')
                                : '--' }}
                        </p>

                        {{-- Trạng thái --}}
                        <span class="inline-block mt-2 text-xs font-medium px-3 py-1 rounded-full
                            @switch($hd['trang_thai'])
                                @case('da_thanh_toan') bg-green-100 text-green-700 @break
                                @case('chua_thanh_toan') bg-red-100 text-red-700 @break
                                @case('mot_phan') bg-yellow-100 text-yellow-700 @break
                                @default bg-gray-200 text-gray-600
                            @endswitch">
                            {{ ucfirst(str_replace('_', ' ', $hd['trang_thai'] ?? 'Không rõ')) }}
                        </span>

                        {{-- Quá hạn --}}
                        @if (!empty($hd['qua_han']) && $hd['qua_han'] === true)
                            <p class="text-xs text-red-600 font-semibold mt-1">⚠️ Đã quá hạn thanh toán</p>
                        @endif

                        <div class="flex justify-end mt-4">
                            <a href="{{ route('khach-thue.hoa-don.show', $hd['id']) }}"
                                class="text-indigo-600 dark:text-indigo-400 text-sm font-semibold hover:underline">
                                Xem chi tiết →
                            </a>
                        </div>
                    </div>

                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-indigo-600/10 to-blue-600/10 transition-all duration-300 pointer-events-none"></div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
