@extends('layouts.chu-tro')
@php use Carbon\Carbon; @endphp

@section('title', 'Quản lý Phòng trọ')

@section('content')
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4">

            <a href="{{ route('chu-tro.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 w-full md:w-auto justify-center">
                <i class="ri-arrow-left-s-line text-lg"></i>
                <span>Quay lại</span>
            </a>

            <h1
                class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3 order-first md:order-none text-center">
                <i class="ri-door-open-line text-indigo-500 text-3xl"></i>
                Quản lý Phòng trọ
            </h1>

            <a href="{{ route('chu-tro.phong.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-300 w-full md:w-auto justify-center">
                <i class="ri-add-line text-lg"></i>
                <span>Thêm phòng mới</span>
            </a>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
<form method="GET" class="mb-6 bg-gray-50 p-4 rounded-xl">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

        {{-- Dãy trọ --}}
        <select name="day_tro_id" class="rounded-lg border-gray-300">
            <option value="">-- Tất cả dãy --</option>
            @foreach($dayTros as $d)
                <option value="{{ $d->id }}"
                    {{ request('day_tro_id') == $d->id ? 'selected' : '' }}>
                    {{ $d->ten_day_tro }}
                </option>
            @endforeach
        </select>

        {{-- Trạng thái --}}
        <select name="trang_thai" class="rounded-lg border-gray-300">
            <option value="">-- Trạng thái --</option>
            <option value="trong" {{ request('trang_thai')=='trong'?'selected':'' }}>Trống</option>
            <option value="da_thue" {{ request('trang_thai')=='da_thue'?'selected':'' }}>Đã thuê</option>
            <option value="bao_tri" {{ request('trang_thai')=='bao_tri'?'selected':'' }}>Bảo trì</option>
        </select>

    </div>

    <div class="mt-4 flex gap-3">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
            🔍 Lọc
        </button>
        <a href="{{ route('chu-tro.phong.index') }}"
           class="px-4 py-2 bg-gray-300 rounded-lg">
            ♻ Reset
        </a>
    </div>
</form>

                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                #
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Thuộc dãy
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Số phòng
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tầng
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Diện tích (m²)
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Giá thuê (VNĐ)
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Sức chứa
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ngày tạo
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ngày cập nhật
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($phongs as $p)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                                    {{ $p->ten_day_tro }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    {{ $p->so_phong }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    {{ $p->tang }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    {{ $p->dien_tich ? number_format($p->dien_tich, 1) : '—' }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-center">
                                                    {{ number_format($p->gia, 0, ',', '.') }} đ
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    {{ $p->suc_chua }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @php
                                                        $colors = [
                                                            'trong' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-100',
                                                            'da_thue' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100',
                                                            'bao_tri' => 'bg-amber-100 text-amber-800 dark:bg-amber-800 dark:text-amber-100',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="px-3 py-1 rounded-full text-xs font-medium {{ $colors[$p->trang_thai] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100' }}">
                                                        {{ match ($p->trang_thai) {
                                'trong' => 'Trống',
                                'da_thue' => 'Đã thuê',
                                'bao_tri' => 'Bảo trì',
                                default => '—',
                            } }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    {{ Carbon::parse($p->ngay_tao)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    {{ Carbon::parse($p->ngay_cap_nhat)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-4">
                                                    <a href="{{ route('chu-tro.phong.show', $p->id) }}"
                                                        class="text-blue-600 hover:text-blue-800 transition-colors" title="Xem chi tiết">
                                                        <i class="ri-eye-line text-xl"></i>
                                                    </a>
                                                    <a href="{{ route('chu-tro.phong.edit', $p->id) }}"
                                                        class="text-indigo-600 hover:text-indigo-800 transition-colors" title="Sửa">
                                                        <i class="ri-edit-line text-xl"></i>
                                                    </a>
                                                    <form action="{{ route('chu-tro.phong.destroy', $p->id) }}" method="POST"
                                                        class="inline-block">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            onclick="return confirm('Xác nhận xóa phòng này? Thao tác này không thể hoàn tác.')"
                                                            class="text-rose-600 hover:text-rose-800 transition-colors" title="Xóa">
                                                            <i class="ri-delete-bin-line text-xl"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="ri-inbox-2-line text-6xl text-gray-400 dark:text-gray-500"></i>
                                        <h3 class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Không tìm thấy phòng nào
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Hãy bắt đầu bằng cách thêm phòng trọ mới.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection