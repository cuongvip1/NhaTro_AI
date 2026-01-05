@extends('layouts.chu-tro')

@section('title', 'Quản lý Tiện ích')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        {{-- 🔹 Tabs điều hướng --}}
        <div class="flex gap-4 border-b border-gray-200 mb-6">
            <a href="{{ route('chu-tro.dich-vu.index') }}"
                class="pb-2 px-3 {{ request()->is('chu-tro/dich-vu') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
                📋 Dịch vụ
            </a>
            <a href="{{ route('chu-tro.dichvu-dinhky.index') }}"
                class="pb-2 px-3 {{ request()->is('chu-tro/dich-vu-dinh-ky*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
                🔁 Dịch vụ định kỳ
            </a>
            <a href="{{ route('chu-tro.tien-ich.index') }}"
                class="pb-2 px-3 {{ request()->is('chu-tro/tien-ich*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
                ⚙️ Tiện ích
            </a>
        </div>

        {{-- 🔹 Nội dung chính --}}
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                ⚙️ Danh sách tiện ích
            </h2>

            {{-- 🔸 Form thêm tiện ích --}}
            <form id="addForm" class="flex items-center gap-3 mb-6">
                <input type="text" name="ten" id="tenTienIch" placeholder="Nhập tên tiện ích..."
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-400 w-1/3">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    ➕ Thêm
                </button>
            </form>

            <p class="text-sm text-gray-500 mb-3">
                Tổng: <span id="countTienIch">0</span> tiện ích
            </p>

            {{-- 🔸 Bảng tiện ích --}}
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 text-left">#</th>
                            <th class="py-3 px-4 text-left">Tên tiện ích</th>
                            <th class="py-3 px-4 text-left">Phòng sử dụng</th>
                            <th class="py-3 px-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="tableTienIch" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const apiUrl = 'http://127.0.0.1:8000/api/chu-tro';
        const token = localStorage.getItem('token');
        let tienIchData = [];

        // 🔄 Load danh sách tiện ích
        async function loadTienIch() {
            try {
                const res = await fetch(`${apiUrl}/tien-ich`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if (res.status === 401) {
                    Swal.fire("Phiên đăng nhập hết hạn", "Vui lòng đăng nhập lại!", "warning")
                        .then(() => window.location.href = "http://127.0.0.1:8001/login");
                    return;
                }

                tienIchData = await res.json();
                const table = document.getElementById('tableTienIch');
                document.getElementById('countTienIch').textContent = tienIchData.length;

                table.innerHTML = tienIchData.length
                    ? tienIchData.map((ti, i) => `
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">${i + 1}</td>
                            <td class="py-3 px-4 font-medium">${ti.ten}</td>
                            <td class="py-3 px-4 text-gray-600">
                                ${ti.phongs && ti.phongs.length
                            ? `<button class="text-indigo-600 hover:underline" onclick="xemPhong(${ti.id})">
                                        Xem (${ti.phongs.length})
                                      </button>`
                            : '<span class="text-gray-400 italic">Chưa có</span>'}
                            </td>
                            <td class="py-3 px-4 text-center flex justify-center gap-3">
                                <button class="px-3 py-1 text-sm bg-yellow-50 text-yellow-700 hover:bg-yellow-100 rounded-md transition"
                                    onclick="suaTienIch(${ti.id}, '${ti.ten.replace(/'/g, "\\'")}')">
                                    ✏️ Sửa
                                </button>
                                <button class="px-3 py-1 text-sm bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition"
                                    onclick="xoaTienIch(${ti.id})">
                                    🗑️ Xóa
                                </button>
                            </td>
                        </tr>`).join('')
                    : `<tr><td colspan="4" class="py-4 text-center text-gray-400 italic">Chưa có tiện ích nào</td></tr>`;
            } catch (error) {
                console.error(error);
                Swal.fire("Lỗi!", "Không thể tải danh sách tiện ích!", "error");
            }
        }

        // 👀 Xem danh sách phòng đang dùng tiện ích
        function xemPhong(id) {
            const ti = tienIchData.find(t => t.id === id);
            const list = ti.phongs && ti.phongs.length
                ? ti.phongs.map(p => `<li>Phòng <b>${p.so_phong}</b></li>`).join('')
                : '<i>Chưa có phòng nào sử dụng</i>';

            Swal.fire({
                title: `Phòng sử dụng "${ti.ten}"`,
                html: `<ul class="list-disc text-left ml-6">${list}</ul>`,
                icon: 'info',
                confirmButtonText: 'Đóng',
                confirmButtonColor: '#4f46e5'
            });
        }

        // 🗑️ Xóa tiện ích
        async function xoaTienIch(id) {
            const confirm = await Swal.fire({
                title: "Xác nhận xóa?",
                text: "Bạn có chắc muốn xóa tiện ích này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e3342f",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "🗑️ Xóa",
                cancelButtonText: "Hủy"
            });
            if (!confirm.isConfirmed) return;

            Swal.fire({ title: "Đang xóa...", didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            const res = await fetch(`${apiUrl}/tien-ich/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });

            Swal.close();

            if (res.ok) {
                Swal.fire("✅ Đã xóa!", "Tiện ích đã được xóa thành công!", "success");
                loadTienIch();
            } else {
                Swal.fire("❌ Lỗi!", "Không thể xóa tiện ích!", "error");
            }
        }

        // ✏️ Sửa tiện ích
        async function suaTienIch(id, tenHienTai) {
            const { value: newTen } = await Swal.fire({
                title: "Chỉnh sửa tiện ích",
                input: "text",
                inputLabel: "Tên tiện ích mới",
                inputValue: tenHienTai,
                showCancelButton: true,
                confirmButtonText: "💾 Lưu",
                cancelButtonText: "Hủy",
                confirmButtonColor: "#4f46e5"
            });
            if (!newTen) return;

            Swal.fire({ title: "Đang cập nhật...", didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            const res = await fetch(`${apiUrl}/tien-ich/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ten: newTen })
            });

            Swal.close();

            if (res.ok) {
                Swal.fire("✅ Thành công!", "Tên tiện ích đã được cập nhật!", "success");
                loadTienIch();
            } else {
                Swal.fire("❌ Lỗi!", "Không thể cập nhật tiện ích!", "error");
            }
        }

        // ➕ Thêm tiện ích mới
        document.getElementById('addForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const ten = document.getElementById('tenTienIch').value.trim();
            if (!ten) return Swal.fire("⚠️ Lỗi!", "Vui lòng nhập tên tiện ích.", "warning");

            Swal.fire({ title: "Đang thêm...", didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            const res = await fetch(`${apiUrl}/tien-ich`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ten })
            });

            Swal.close();

            if (res.ok) {
                Swal.fire("🎉 Thành công!", "Đã thêm tiện ích mới!", "success");
                document.getElementById('tenTienIch').value = '';
                loadTienIch();
            } else {
                Swal.fire("❌ Lỗi!", "Không thể thêm tiện ích!", "error");
            }
        });

        // 🚀 Tải dữ liệu khi mở trang
        loadTienIch();
    </script>
@endsection