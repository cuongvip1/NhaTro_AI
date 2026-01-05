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
                    🏠 Dịch vụ định kỳ cho phòng #{{ $phong_id }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    Quản lý các dịch vụ được tính định kỳ hàng tháng cho phòng trọ này
                </p>
            </div>

            <a href="{{ route('chu-tro.dichvu-dinhky.index') }}"
                class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-lg shadow-sm transition">
                ⬅️ <span class="ml-1">Danh sách</span>
            </a>
        </div>

        {{-- 📋 Bảng danh sách dịch vụ --}}
        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Tên dịch vụ</th>
                        <th class="py-3 px-4 text-center font-semibold">Đơn vị</th>
                        <th class="py-3 px-4 text-center font-semibold">Đơn giá (VNĐ)</th>
                        <th class="py-3 px-4 text-center font-semibold">Số lượng</th>
                        <th class="py-3 px-4 text-center font-semibold">Thành tiền</th>
                        <th class="py-3 px-4 text-center font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($dich_vus as $dv)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-medium">{{ $dv->ten }}</td>
                            <td class="py-3 px-4 text-center">{{ $dv->don_vi }}</td>
                            <td class="py-3 px-4 text-center">{{ number_format($dv->don_gia, 0, ',', '.') }} ₫</td>
                            <td class="py-3 px-4 text-center">{{ $dv->so_luong }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-indigo-600">
                                {{ number_format($dv->don_gia * $dv->so_luong, 0, ',', '.') }} ₫
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button data-id="{{ $dv->id }}"
                                    class="btn-delete inline-flex items-center text-red-600 hover:text-red-800 font-medium transition">
                                    🗑️ Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-400 italic">
                                Chưa có dịch vụ định kỳ nào cho phòng này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ➕ Thêm dịch vụ mới --}}
        <div class="mt-10 border-t pt-6">
            <h4 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
                ➕ Thêm dịch vụ định kỳ mới
            </h4>

            <form id="addForm" action="{{ route('chu-tro.dichvu-dinhky.store', $phong_id) }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-700 mb-1 font-medium">Dịch vụ</label>
                    <select name="dich_vu_id"
                        class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Chọn dịch vụ --</option>
                        @foreach ($tatCaDv as $dv)
                            <option value="{{ $dv->id }}">{{ $dv->ten }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1 font-medium">Đơn giá (VNĐ)</label>
                    <input type="number" step="100" min="0" name="don_gia"
                        class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1 font-medium">Số lượng</label>
                    <input type="number" step="0.1" min="0.1" name="so_luong"
                        class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold shadow-sm">
                        💾 Lưu dịch vụ
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // 🗑️ Xóa dịch vụ
            document.querySelectorAll(".btn-delete").forEach(btn => {
                btn.addEventListener("click", async (e) => {
                    const id = e.target.getAttribute("data-id");
                    Swal.fire({
                        title: "Xác nhận xóa?",
                        text: "Dịch vụ này sẽ bị xóa khỏi phòng!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#e3342f",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "🗑️ Xóa",
                        cancelButtonText: "Hủy"
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            const res = await fetch(`/chu-tro/dich-vu-dinh-ky/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            if (res.ok) {
                                Swal.fire("Đã xóa!", "Dịch vụ định kỳ đã được xóa.", "success")
                                    .then(() => location.reload());
                            } else {
                                Swal.fire("Lỗi!", "Không thể xóa dịch vụ này.", "error");
                            }
                        }
                    });
                });
            });

            // 💾 Thêm mới
            document.getElementById("addForm").addEventListener("submit", async function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);

                Swal.fire({
                    title: "Đang thêm...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const res = await fetch(form.action, {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: formData
                });

                if (res.ok) {
                    Swal.fire("Thành công!", "Đã thêm dịch vụ định kỳ cho phòng!", "success")
                        .then(() => location.reload());
                } else {
                    Swal.fire("Lỗi!", "Không thể thêm dịch vụ. Vui lòng kiểm tra lại.", "error");
                }
            });
        });
    </script>
@endsection