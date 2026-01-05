@extends('layouts.chu-tro')

@section('title', 'Yêu cầu thuê')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
        <i class="ri-mail-open-line"></i> Danh sách yêu cầu thuê
    </h1>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-900/5">
        <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
            <thead class="bg-gray-50 dark:bg-gray-700/40">
                <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Phòng</th>
                    <th class="p-3 text-left">Khách thuê</th>
                    <th class="p-3 text-left">Ghi chú</th>
                    <th class="p-3 text-left">Ngày tạo</th>
                    <th class="p-3 text-center">Trạng thái</th>
                    <th class="p-3 text-center">Thao tác</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($yeu_cau as $row)
                    @php 
                        $st = $row['trang_thai'] ?? 'cho_duyet'; 
                        $badgeClass = match($st) {
                            'cho_duyet' => 'bg-yellow-100 text-yellow-700',
                            'chap_nhan' => 'bg-blue-100 text-blue-700',
                            'da_tao_hop_dong' => 'bg-green-100 text-green-700',
                            'tu_choi' => 'bg-red-100 text-red-700',
                            'huy' => 'bg-gray-200 text-gray-700',
                             'chu_tro_huy_hop_dong' => 'bg-red-200 text-red-800',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $row['so_phong'] ?? '' }} — {{ $row['ten_day_tro'] ?? '' }}</td>
                        <td class="p-3">{{ $row['khach_thue'] ?? '' }}</td>
                        <td class="p-3">{{ $row['ghi_chu'] ?? '' }}</td>
                        <td class="p-3">
                            {{ \Carbon\Carbon::parse($row['ngay_tao'])->format('d/m/Y H:i') }}
                        </td>

                        <td class="p-3 text-center">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                {{ str_replace('_', ' ', ucfirst($st)) }}
                            </span>
                        </td>

                        <td class="p-3 text-center space-x-2">
                            {{-- 👁 Xem chi tiết --}}
                            <a href="{{ route('chu-tro.yeu-cau-thue.show', $row['id']) }}" 
                               class="text-indigo-600 hover:underline font-medium">
                                👁 Xem
                            </a>

                            @if ($st === 'cho_duyet')
                                {{-- ✅ Chấp nhận --}}
                                <form action="{{ route('chu-tro.yeu-cau-thue.chap-nhan', $row['id']) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-green-600 hover:underline"
                                        onclick="return confirm('Chấp nhận yêu cầu này và tạo hợp đồng?')">
                                        Chấp nhận
                                    </button>
                                </form>

                                {{-- ❌ Từ chối --}}
                                <form action="{{ route('chu-tro.yeu-cau-thue.tu-choi', $row['id']) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-red-600 hover:underline"
                                        onclick="return confirm('Từ chối yêu cầu này?')">
                                        Từ chối
                                    </button>
                                </form>

                            @elseif ($st === 'da_tao_hop_dong')
                                <span class="text-green-600 font-medium">Đã tạo hợp đồng</span>
                            @elseif ($st === 'chap_nhan')
                                <span class="text-blue-600 font-medium">Đã chấp nhận</span>
                            @elseif ($st === 'tu_choi')
                                <span class="text-red-600 font-medium">Đã từ chối</span>
                            @elseif ($st === 'chu_tro_huy_hop_dong')
                                <span class="text-red-600 font-medium">Chủ trọ đã hủy hợp đồng</span>
                            @elseif ($st === 'huy')
                                <span class="text-gray-600 font-medium">Khách đã hủy yêu cầu</span>
                            @else
                                <span class="text-gray-500 font-medium">Không xác định</span>
                            @endif

                            
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center p-6 text-gray-500">
                            Không có yêu cầu thuê nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
