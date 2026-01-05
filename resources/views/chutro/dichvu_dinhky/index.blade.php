@extends('layouts.chu-tro')

@section('title', 'Dịch vụ định kỳ')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">

        {{-- 🔹 Tabs chuyển đổi --}}
        <div class="flex gap-4 border-b border-gray-200 mb-6">
            <a href="{{ route('chu-tro.dich-vu.index') }}"
                class="pb-2 px-3 font-semibold text-sm transition
                   {{ request()->is('chu-tro/dich-vu') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
                📋 Dịch vụ
            </a>
            <a href="{{ route('chu-tro.dichvu-dinhky.index') }}"
                class="pb-2 px-3 font-semibold text-sm transition
                   {{ request()->is('chu-tro/dich-vu-dinh-ky*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
                🔁 Dịch vụ định kỳ
            </a>
        </div>

        {{-- 🧾 Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    🔁 Quản lý dịch vụ định kỳ
                </h1>
                <p class="text-gray-500 text-sm mt-1">Danh sách phòng trọ và các dịch vụ định kỳ đang áp dụng</p>
            </div>
        </div>

        {{-- 📋 Bảng danh sách phòng --}}
        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Dãy trọ</th>
                        <th class="py-3 px-4 text-left font-semibold">Phòng</th>
                        <th class="py-3 px-4 text-center font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($phongs as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-medium">{{ $p->ten_day_tro }}</td>
                            <td class="py-3 px-4">{{ $p->so_phong }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('chu-tro.dichvu-dinhky.show', $p->id) }}"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium shadow-sm">
                                    🔍 Xem dịch vụ
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-400 italic">
                                Không có phòng nào thuộc chủ trọ này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔙 Nút quay lại (nếu muốn thêm) --}}
        <div class="mt-6 text-center">
            <a href="{{ route('chu-tro.dashboard') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition">
                ⬅️ Quay lại trang chủ
            </a>
        </div>
    </div>
@endsection