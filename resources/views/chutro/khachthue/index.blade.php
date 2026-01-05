@extends('layouts.chu-tro')
@section('title', 'Quản lý khách thuê')

@section('content')
    <div class="max-w-7xl mx-auto p-6 space-y-6">

        {{-- 🔹 Tiêu đề + nút thêm --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="ri-user-line"></i> Danh sách khách thuê
            </h1>
            <a href="{{ route('chu-tro.khachthue.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white rounded-lg shadow hover:scale-[1.03] transition-all">
                <i class="ri-user-add-line text-lg"></i> Thêm khách thuê
            </a>
        </div>

        {{-- 🔹 Bộ lọc dãy trọ --}}
        <form method="GET" class="flex items-center gap-3 mb-4">
            <label class="font-medium text-gray-700">Chọn dãy trọ:</label>
            <select name="day_tro_id" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tất cả</option>
                @foreach ($day_tros as $day)
                    <option value="{{ $day->id }}" {{ request('day_tro_id') == $day->id ? 'selected' : '' }}>
                        {{ $day->ten_day_tro }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- 🔹 Bảng danh sách --}}
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-900/5">
            <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-700/40">
                    <tr>
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Họ tên</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">SĐT</th>
                        <th class="p-3 text-left">Phòng</th>
                        <th class="p-3 text-center">Trạng thái</th>
                        <th class="p-3 text-center">Thao tác</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($khach as $k)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                            <td class="p-3 text-center">{{ $loop->iteration }}</td>
                            <td class="p-3 font-medium">{{ $k['ho_ten'] ?? 'N/A' }}</td>
                            <td class="p-3">{{ $k['email'] ?? '—' }}</td>
                            <td class="p-3">{{ $k['so_dien_thoai'] ?? '—' }}</td>
                            <td class="p-3">{{ $k['so_phong'] ?? '—' }}</td>

                            {{-- Trạng thái --}}
                            <td class="p-3 text-center">
                                @php
                                    $status = strtolower($k['trang_thai_thue'] ?? 'không có hợp đồng');
                                    $color = match ($status) {
                                        'đang thuê' => 'bg-green-100 text-green-700',
                                        'sắp hết hạn' => 'bg-yellow-100 text-yellow-700',
                                        'đã hết hạn' => 'bg-gray-200 text-gray-700',
                                        default => 'bg-red-100 text-red-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            {{-- Thao tác --}}
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-4">

                                    {{-- Xem --}}
                                    <a href="{{ route('chu-tro.khachthue.show', $k['id']) }}" title="Xem chi tiết"
                                        class="text-indigo-600 hover:text-indigo-800 transition">
                                        <i class="ri-eye-line text-lg"></i>
                                    </a>

                                    {{-- Sửa --}}
                                    <a href="{{ route('chu-tro.khachthue.edit', $k['id']) }}" title="Chỉnh sửa"
                                        class="text-yellow-500 hover:text-yellow-600 transition">
                                        <i class="ri-pencil-line text-lg"></i>
                                    </a>

                                    {{-- Xóa --}}
                                    <form action="{{ route('chu-tro.khachthue.destroy', $k['id']) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Xóa khách thuê"
                                            onclick="return confirm('Bạn chắc chắn muốn xóa khách thuê này?')"
                                            class="text-red-500 hover:text-red-700 transition">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-6 text-gray-500">Không có khách thuê nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection