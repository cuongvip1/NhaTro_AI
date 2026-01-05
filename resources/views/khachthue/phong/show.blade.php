@extends('layouts.tenant-layout')

@section('title', 'Chi tiết phòng')
@section('page_title', 'Thông tin phòng đang thuê')

@section('tenant_content')
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">

        {{-- ========== ẢNH PHÒNG ========== --}}
        @php
            // ✅ Lấy bài đăng đang hiển thị (trang_thai = "dang")
            $baiDangHienTai = collect($phong['bai_dang'] ?? [])
                ->where('trang_thai', 'dang')
                ->sortByDesc('ngay_cap_nhat')
                ->first();

            // ✅ Lấy danh sách ảnh của bài đăng đó
            $anhList = collect($baiDangHienTai['anh'] ?? [])->pluck('url')->filter()->toArray();
        @endphp

        @if(count($anhList) > 0)
            {{-- Gallery hiển thị nhiều ảnh --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 dark:bg-gray-900 p-4">
                @foreach ($anhList as $anh)
                    <img src="{{ $anh }}" alt="Ảnh phòng"
                        class="w-full h-48 object-cover rounded-lg shadow hover:scale-105 transition-transform duration-300">
                @endforeach
            </div>
        @else
            {{-- Nếu không có ảnh --}}
            <img src="/images/default-room.jpg" alt="Ảnh phòng" class="w-full h-64 object-cover">
        @endif

        {{-- ========== THÔNG TIN PHÒNG ========== --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Cột trái --}}
            <div class="space-y-3 text-gray-700 dark:text-gray-300">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">
                    🏠 {{ $phong['so_phong'] ?? $phong['ten_phong'] ?? 'Phòng trọ' }}
                </h2>

                {{-- Tiêu đề bài đăng --}}
                @if(!empty($baiDangHienTai['tieu_de']))
                    <p class="text-gray-500 text-sm italic">{{ $baiDangHienTai['tieu_de'] }}</p>
                @endif

                <p>💰 <strong>Giá thuê:</strong> {{ number_format($phong['gia'] ?? 0) }}đ / tháng</p>
                <p>📏 <strong>Diện tích:</strong> {{ $phong['dien_tich'] ?? '--' }} m²</p>
                <p>🏢 <strong>Tầng:</strong> {{ $phong['tang'] ?? '--' }}</p>
                <p>👥 <strong>Sức chứa:</strong> {{ $phong['suc_chua'] ?? '--' }} người</p>

                <p>⚡ <strong>Trạng thái:</strong>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ ($phong['trang_thai'] ?? '') === 'da_thue'
        ? 'bg-green-100 text-green-700'
        : 'bg-gray-200 text-gray-600' }}">
                        {{ ucfirst(str_replace('_', ' ', $phong['trang_thai'] ?? 'Không rõ')) }}
                    </span>
                </p>

                {{-- Mô tả bài đăng --}}
                @if(!empty($baiDangHienTai['mo_ta']))
                    <div class="mt-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">📝 Mô tả chi tiết</h4>
                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed">
                            {{ $baiDangHienTai['mo_ta'] }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Cột phải --}}
            <div class="space-y-3">
                <h3 class="font-semibold text-gray-800 dark:text-white text-lg mb-2">
                    📍 Thông tin dãy trọ
                </h3>
                <p><strong>Tên dãy:</strong> {{ $phong['day_tro']['ten_day_tro'] ?? 'Không xác định' }}</p>
                <p><strong>Địa chỉ:</strong> {{ $phong['day_tro']['dia_chi'] ?? '--' }}</p>
                <p><strong>Mô tả:</strong> {{ $phong['day_tro']['mo_ta'] ?? 'Không có mô tả.' }}</p>
                <p><strong>Tiện ích:</strong> {{ $phong['day_tro']['tien_ich'] ?? 'Không có thông tin.' }}</p>
            </div>
        </div>

        {{-- ========== NÚT QUAY LẠI ========== --}}
        <div class="px-6 pb-6 flex justify-end">
            <a href="{{ route('khach-thue.phong.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">
                <i class="ri-arrow-left-line"></i> Quay lại danh sách
            </a>
        </div>
    </div>
@endsection