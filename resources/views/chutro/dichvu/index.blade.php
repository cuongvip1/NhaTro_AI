@extends('layouts.chu-tro')

@section('title', 'Quản lý dịch vụ')

@section('content')
    <div x-data="{ search: '' }" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">

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
            <a href="{{ route('chu-tro.tien-ich.index') }}"
                class="pb-2 px-3 font-semibold text-sm transition
                   {{ request()->is('chu-tro/tien-ich*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
                ⚙️ Tiện ích
            </a>
        </div>

        {{-- 🧾 Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    ⚙️ Quản lý dịch vụ
                </h1>
                <p class="text-gray-500 text-sm mt-1">Thêm, sửa, hoặc xóa các dịch vụ bạn đang cung cấp</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <input type="text" x-model="search" placeholder="🔍 Tìm theo tên dịch vụ..."
                    class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 w-full sm:w-64 transition" />

                <a href="{{ route('chu-tro.dich-vu.create') }}"
                    class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition">
                    ➕ <span class="ml-1">Thêm dịch vụ</span>
                </a>
            </div>
        </div>

        {{-- 📋 Bảng dịch vụ --}}
        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-gray-700 uppercase text-xs tracking-wider">
                        <th class="px-4 py-3 text-center font-semibold">#</th>
                        <th class="px-4 py-3 text-left font-semibold">Mã</th>
                        <th class="px-4 py-3 text-left font-semibold">Tên dịch vụ</th>
                        <th class="px-4 py-3 text-left font-semibold">Đơn vị</th>
                        <th class="px-4 py-3 text-right font-semibold">Đơn giá (VNĐ)</th>
                        <th class="px-4 py-3 text-center font-semibold">Có đồng hồ</th>
                        <th class="px-4 py-3 text-center font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($dich_vus as $index => $dv)
                        <tr x-show="{{ json_encode(strtolower($dv->ten)) }}.includes(search.toLowerCase())"
                            class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $dv->ma }}</td>
                            <td class="px-4 py-3 font-medium">{{ $dv->ten }}</td>
                            <td class="px-4 py-3">
                                {{ $dv->don_vi }}
                                @if(strtolower($dv->don_vi) == 'kwh')
                                    <span class="text-xs text-gray-500">(điện - kilowatt giờ)</span>
                                @elseif(strtolower($dv->don_vi) == 'm3' || strtolower($dv->don_vi) == 'm³')
                                    <span class="text-xs text-gray-500">(nước - mét khối)</span>
                                @elseif(strtolower($dv->don_vi) == 'tháng')
                                    <span class="text-xs text-gray-500">(tính theo tháng)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                {{ number_format($dv->don_gia, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($dv->co_dong_ho)
                                    <span class="text-green-600 font-semibold">✅ Có</span>
                                @else
                                    <span class="text-gray-400">❌ Không</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center flex justify-center gap-2">
                                <a href="{{ route('chu-tro.dich-vu.edit', $dv->id) }}"
                                    class="inline-flex items-center bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-medium px-3 py-1.5 rounded-md shadow-sm transition">
                                    ✏️ Sửa
                                </a>

                                <form action="{{ route('chu-tro.dich-vu.destroy', $dv->id) }}" method="POST"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="delete-btn inline-flex items-center bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-3 py-1.5 rounded-md shadow-sm transition">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-400 italic">
                                Chưa có dịch vụ nào được thêm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ⚡ Script --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const form = btn.closest('form');

                    const result = await Swal.fire({
                        title: 'Xác nhận xóa?',
                        text: 'Bạn có chắc muốn xóa dịch vụ này không?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '🗑️ Xóa',
                        cancelButtonText: 'Hủy',
                    });

                    if (result.isConfirmed) form.submit();
                });
            });

            // ✅ Thông báo sau khi thêm / xóa dịch vụ
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#16a34a',
                });
            @elseif(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#dc2626',
                });
            @endif
                            });
    </script>
@endsection