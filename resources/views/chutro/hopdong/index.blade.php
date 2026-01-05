@extends('layouts.chu-tro')

@section('title', 'Quản lý hợp đồng thuê')

@section('content')
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        {{-- Tiêu đề và nút thêm --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="ri-file-list-2-line"></i> Danh sách hợp đồng thuê
            </h1>
            <a href="{{ route('chu-tro.hop-dong.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white rounded-lg shadow hover:scale-[1.03] transition">
                <i class="ri-add-line"></i> Thêm hợp đồng mới
            </a>
        </div>

        {{-- Bộ lọc theo dãy trọ --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('chu-tro.hop-dong.index') }}" class="flex items-center gap-3">
                <label class="font-medium text-gray-700">Chọn dãy trọ:</label>
                <select name="day_tro_id" onchange="this.form.submit()" class="border border-gray-300 rounded p-2">
                    <option value="">Tất cả</option>
                    @foreach($day_tros as $day)
                        <option value="{{ $day->id }}" {{ request('day_tro_id') == $day->id ? 'selected' : '' }}>
                            {{ $day->ten_day_tro }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- 🔹 Bảng danh sách hợp đồng --}}
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-900/5">
            <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-700/40">
                    <tr>
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Phòng</th>
                        <th class="p-3 text-left">Khách thuê</th>
                        <th class="p-3 text-left">Thời gian thuê</th>
                        <th class="p-3 text-right">Tiền cọc</th>
                        <th class="p-3 text-left">Ngày tạo</th>
                        <th class="p-3 text-left">File hợp đồng</th>
                        <th class="p-3 text-center">Trạng thái</th>
                        <th class="p-3 text-center">Thao tác</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($hop_dong as $hd)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                                        <td class="p-3">{{ $loop->iteration }}</td>

                                        {{-- Phòng --}}
                                        <td class="p-3 font-medium">{{ $hd['so_phong'] ?? 'N/A' }}</td>

                                        {{-- Khách thuê --}}
                                        <td class="p-3">{{ $hd['khach_thue'] ?? 'N/A' }}</td>

                                        {{-- Thời gian thuê --}}
                                        <td class="p-3">
                                            {{ \Carbon\Carbon::parse($hd['ngay_bat_dau'])->format('d/m/Y') }} →
                                            {{ \Carbon\Carbon::parse($hd['ngay_ket_thuc'])->format('d/m/Y') }}
                                        </td>

                                        <td class="p-3 text-right">
                                            @if(isset($hd['tien_coc']) && $hd['tien_coc'] > 0)
                                                {{ number_format($hd['tien_coc'], 0, ',', '.') }} ₫
                                            @else
                                                <span class="text-gray-400 italic">Chưa đặt cọc</span>
                                            @endif
                                        </td>


                                        {{-- Ngày tạo --}}
                                        <td class="p-3">
                                            {{ isset($hd['ngay_tao']) ? \Carbon\Carbon::parse($hd['ngay_tao'])->format('d/m/Y H:i') : '-' }}
                                        </td>

                                        {{-- File hợp đồng --}}
                                        <td class="p-3">
                                            @if (!empty($hd['url_file_hop_dong']))
                                                <a href="{{ env('API_BASE_URL') . '/storage/' . $hd['url_file_hop_dong'] }}" target="_blank"
                                                    class="text-indigo-600 hover:underline">
                                                    {{ basename($hd['url_file_hop_dong']) }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 italic">Chưa có file</span>
                                            @endif
                                        </td>
                                        {{-- Trạng thái --}}
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                                                                                                                                                                    {{ match ($hd['trang_thai']) {
                            'hieu_luc' => 'bg-green-100 text-green-700',
                            'ket_thuc' => 'bg-gray-200 text-gray-700',
                            default => 'bg-red-100 text-red-700'
                        } }}">
                                                {{ ucfirst($hd['trang_thai']) }}
                                            </span>
                                        </td>

                                        {{-- Thao tác --}}
                                        <td class="p-3 text-center space-x-2">
                                            <a href="{{ route('chu-tro.hop-dong.show', $hd['id']) }}"
                                                class="text-indigo-600 hover:underline">Xem</a>
                                            <a href="{{ route('chu-tro.hop-dong.edit', $hd['id']) }}"
                                                class="text-yellow-600 hover:underline">Sửa</a>
                                            <form action="{{ route('chu-tro.hop-dong.destroy', $hd['id']) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline"
                                                    onclick="return confirm('Bạn có chắc muốn xóa hợp đồng này?')">
                                                    Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-6 text-gray-500">
                                💤 Chưa có hợp đồng nào được tạo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection