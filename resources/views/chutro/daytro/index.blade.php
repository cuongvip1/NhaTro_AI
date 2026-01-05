@extends('layouts.chu-tro')
@php use Illuminate\Support\Str; @endphp

@section('title', 'Quản lý Dãy trọ')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                {{-- 🏠 Nút quay về Trang chủ --}}
                <a href="{{ route('chu-tro.dashboard') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition">
                    <i class="ri-arrow-left-line text-lg text-gray-700 dark:text-gray-300"></i>
                    <span class="font-medium text-gray-700 dark:text-gray-200">Trang chủ</span>
                </a>

                {{-- Tiêu đề --}}
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="ri-community-line text-indigo-500 text-3xl"></i>
                    Danh sách Dãy trọ
                </h1>
            </div>

            {{-- Nút thêm mới --}}
            <a href="{{ route('chu-tro.day-tro.create') }}"
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                <i class="ri-add-line mr-1"></i> Thêm dãy trọ
            </a>
        </div>


        {{-- Bảng danh sách --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6 overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Tên dãy trọ</th>
                        <th class="p-3 text-left">Địa chỉ</th>
                        <th class="p-3 text-center">Số phòng</th>
                        <th class="p-3 text-center">Diện tích TB</th>
                        <th class="p-3 text-center">Giá TB</th>
                        <th class="p-3 text-left">Tiện ích</th>
                        <th class="p-3 text-left">Mô tả</th>
                        <th class="p-3 text-center">Ngày tạo</th>
                        <th class="p-3 text-center">Ngày cập nhật</th>
                        <th class="p-3 text-center">Thao tác</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($dayTroList as $day)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                            {{-- STT --}}
                            <td class="p-3">{{ $loop->iteration }}</td>

                            {{-- Tên dãy --}}
                            <td class="p-3 font-semibold text-gray-800 dark:text-gray-100">
                                {{ $day->ten_day_tro }}
                            </td>

                            {{-- Địa chỉ --}}
                            <td class="p-3">{{ $day->dia_chi }}</td>

                            {{-- Số phòng --}}
                            <td class="p-3 text-center">
                                {{ $day->so_phong ?? '—' }}
                            </td>

                            {{-- Diện tích trung bình --}}
                            <td class="p-3 text-center">
                                {{ $day->dien_tich_tb ? $day->dien_tich_tb . ' m²' : '—' }}
                            </td>

                            {{-- Giá trung bình --}}
                            <td class="p-3 text-center">
                                {{ $day->gia_trung_binh ? number_format($day->gia_trung_binh, 0, ',', '.') . ' đ' : '—' }}
                            </td>

                            {{-- Tiện ích --}}
                            <td class="p-3 max-w-[220px] truncate" title="{{ $day->tien_ich }}">
                                {{ $day->tien_ich ?? '—' }}
                            </td>

                            {{-- Mô tả (có xem thêm/thu gọn) --}}
                            <td class="p-3 max-w-[250px] align-top">
                                <div x-data="{ open: false }">
                                    <p x-show="!open" class="truncate" x-text="'{{ Str::limit($day->mo_ta, 70) }}'"></p>
                                    <p x-show="open" x-text="'{{ addslashes($day->mo_ta) }}'"></p>
                                    @if($day->mo_ta)
                                        <button @click="open = !open" class="text-indigo-500 text-xs hover:underline mt-1">
                                            <span x-text="open ? 'Thu gọn' : 'Xem thêm'"></span>
                                        </button>
                                    @endif
                                </div>
                            </td>

                            {{-- Ngày tạo --}}
                            <td class="p-3 text-center">
                                {{ $day->ngay_tao ? \Carbon\Carbon::parse($day->ngay_tao)->format('d/m/Y') : '—' }}
                            </td>

                            {{-- Ngày cập nhật --}}
                            <td class="p-3 text-center">
                                {{ $day->ngay_cap_nhat ? \Carbon\Carbon::parse($day->ngay_cap_nhat)->format('d/m/Y') : '—' }}
                            </td>

                            {{-- Thao tác --}}
                            <td class="p-3 text-center space-x-2">
                                {{-- Xem chi tiết --}}
                                <a href="{{ route('chu-tro.day-tro.show', $day->id) }}"
                                    class="text-emerald-600 hover:text-emerald-800" title="Xem chi tiết">
                                    <i class="ri-eye-line text-lg"></i>
                                </a>

                                {{-- Sửa --}}
                                <a href="{{ route('chu-tro.day-tro.edit', $day->id) }}"
                                    class="text-indigo-600 hover:text-indigo-800" title="Sửa">
                                    <i class="ri-edit-line text-lg"></i>
                                </a>

                                {{-- Xóa --}}
                                <form action="{{ route('chu-tro.day-tro.destroy', $day->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Xác nhận xóa dãy này?')"
                                        class="text-rose-600 hover:text-rose-800" title="Xóa">
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-4 text-center text-gray-500">
                                Chưa có dãy trọ nào được thêm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection