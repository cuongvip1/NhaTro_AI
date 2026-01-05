@extends('layouts.chu-tro')

@section('title', 'Chi tiết Dãy trọ')

@section('content')
    <div class="max-w-6xl mx-auto py-8 px-6">
        {{-- 🔹 Header --}}
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold flex items-center gap-2 text-gray-800 dark:text-gray-100">
                <i class="ri-community-line text-indigo-500 text-3xl"></i>
                {{ $dayTro->ten_day_tro }}
            </h1>
            <div class="space-x-2">
                <a href="{{ route('chu-tro.day-tro.edit', $dayTro->id) }}"
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                    <i class="ri-edit-line mr-1"></i> Sửa thông tin
                </a>
                <a href="{{ route('chu-tro.day-tro.index') }}"
                    class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    <i class="ri-arrow-left-line mr-1"></i> Quay lại
                </a>
            </div>
        </div>

        {{-- 🏠 Thông tin chi tiết --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="font-semibold text-gray-700 dark:text-gray-200 mb-2">Thông tin cơ bản</h2>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li><strong>Địa chỉ:</strong> {{ $dayTro->dia_chi }}</li>
                        <li><strong>Số phòng:</strong> {{ $dayTro->so_phong ?? '—' }}</li>
                        <li><strong>Diện tích TB:</strong> {{ $dayTro->dien_tich_tb ? $dayTro->dien_tich_tb . ' m²' : '—' }}
                        </li>
                        <li><strong>Giá TB:</strong>
                            {{ $dayTro->gia_trung_binh ? number_format($dayTro->gia_trung_binh, 0, ',', '.') . ' đ' : '—' }}
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-700 dark:text-gray-200 mb-2">Thông tin thêm</h2>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li><strong>Ngày tạo:</strong> {{ \Carbon\Carbon::parse($dayTro->ngay_tao)->format('d/m/Y H:i') }}
                        </li>
                        <li><strong>Ngày cập nhật:</strong>
                            {{ \Carbon\Carbon::parse($dayTro->ngay_cap_nhat)->format('d/m/Y H:i') }}</li>
                    </ul>
                </div>
            </div>

            @if($dayTro->mo_ta)
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-1">Mô tả</h3>
                    <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $dayTro->mo_ta }}</p>
                </div>
            @endif

            @if($dayTro->tien_ich)
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-1">Tiện ích</h3>
                    <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $dayTro->tien_ich }}</p>
                </div>
            @endif
        </div>

        {{-- 🧱 Danh sách phòng --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <i class="ri-door-line text-indigo-500"></i>
                    Phòng thuộc dãy này
                </h2>
                <a href="{{ route('chu-tro.phong.index') }}"
                    class="px-3 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                    <i class="ri-add-line mr-1"></i> Thêm phòng mới
                </a>
            </div>

            @if($dayTro->phong && $dayTro->phong->count() > 0)
                <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/40">
                        <tr>
                            <th class="p-3 text-left">#</th>
                            <th class="p-3 text-left">Số phòng</th>
                            <th class="p-3 text-center">Diện tích</th>
                            <th class="p-3 text-center">Giá (VNĐ)</th>
                            <th class="p-3 text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($dayTro->phong as $p)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                                        <td class="p-3">{{ $loop->iteration }}</td>
                                        <td class="p-3 font-medium">{{ $p->so_phong }}</td>
                                        <td class="p-3 text-center">{{ $p->dien_tich ? $p->dien_tich . ' m²' : '—' }}</td>
                                        <td class="p-3 text-center">{{ $p->gia ? number_format($p->gia, 0, ',', '.') . ' đ' : '—' }}</td>
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                                                                                    {{ match ($p->trang_thai) {
                                'trong' => 'bg-amber-100 text-amber-700',
                                'da_thue' => 'bg-emerald-100 text-emerald-700',
                                'dang_sua' => 'bg-rose-100 text-rose-700',
                                default => 'bg-gray-100 text-gray-700'
                            } }}">
                                                {{ ucfirst(str_replace('_', ' ', $p->trang_thai)) }}
                                            </span>
                                        </td>
                                    </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-sm">Chưa có phòng nào trong dãy này.</p>
            @endif
        </div>
    </div>
@endsection