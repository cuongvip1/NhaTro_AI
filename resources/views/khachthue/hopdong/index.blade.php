@extends('layouts.tenant-layout')

@section('title', 'Hợp đồng thuê')
@section('page_title', 'Danh sách hợp đồng')

@section('tenant_content')
        {{-- 🔝 Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="ri-file-text-line text-indigo-600 text-2xl"></i>
                Hợp đồng của bạn
            </h2>
        </div>

        {{-- 📄 Danh sách hợp đồng --}}
        @if(empty($hopDong) || count($hopDong) === 0)
            <div class="flex items-center justify-center h-40 text-gray-500 italic">
                Hiện bạn chưa có hợp đồng nào.
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead
                        class="bg-gradient-to-r from-indigo-50 to-blue-50 text-indigo-700 font-medium uppercase text-xs border-b">
                        <tr>
                            <th class="px-5 py-3">Phòng</th>
                            <th class="px-5 py-3">Dãy trọ</th>
                            <th class="px-5 py-3">Ngày bắt đầu</th>
                            <th class="px-5 py-3">Ngày kết thúc</th>
                            <th class="px-5 py-3 text-center">Trạng thái</th>
                            <th class="px-5 py-3 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hopDong as $hd)
                            @php
                                // Use data_get so this view works with arrays or objects (models/arrays)
                                $phong = data_get($hd, 'phong', []);
                                $dayTro = data_get($phong, 'day_tro', []);
                                $status = data_get($hd, 'trang_thai', 'khong_xac_dinh');
                                $badge = [
                                    'hieu_luc' => 'bg-green-100 text-green-700',
                                    'da_het_han' => 'bg-gray-200 text-gray-700',
                                    'huy' => 'bg-red-100 text-red-600',
                                ][$status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <tr class="border-b hover:bg-gray-50 transition-colors duration-150">
                                {{-- 🏠 Phòng --}}
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    {{ data_get($phong, 'so_phong', '—') }}
                                </td>

                                {{-- 🏢 Dãy trọ --}}
                                <td class="px-5 py-3 text-gray-700">
                                    {{ data_get($dayTro, 'ten_day_tro', '—') }}
                                </td>

                                {{-- 📅 Ngày bắt đầu --}}
                                <td class="px-5 py-3 text-gray-600">
                                    @php $nbd = data_get($hd, 'ngay_bat_dau'); @endphp
                                    {{ $nbd ? \Carbon\Carbon::parse($nbd)->format('d/m/Y') : '—' }}
                                </td>

                                {{-- 📅 Ngày kết thúc --}}
                                <td class="px-5 py-3 text-gray-600">
                                    @php $nkt = data_get($hd, 'ngay_ket_thuc'); @endphp
                                    {{ $nkt ? \Carbon\Carbon::parse($nkt)->format('d/m/Y') : '—' }}
                                </td>

                                {{-- ✅ Trạng thái --}}
                                <td class="px-5 py-3 text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>

                                {{-- 🔍 Xem chi tiết --}}
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $hdId = data_get($hd, 'id') ?? data_get($hd, 'hop_dong_id') ?? null;
                                    @endphp
                                    @if($hdId)
                                        <a href="{{ route('khach-thue.hop-dong.show', $hdId) }}"
                                            class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-150">
                                            Xem chi tiết
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
@endsection