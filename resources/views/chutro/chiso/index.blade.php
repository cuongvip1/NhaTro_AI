@extends('layouts.chu-tro')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto space-y-6">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ri-flashlight-fill text-yellow-500 text-3xl"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Quản lý chỉ số điện nước</h1>
                </div>
                <a href="{{ route('chu-tro.dashboard') }}" class="text-sm text-indigo-600 hover:underline">
                    ← Quay lại
                </a>
            </div>

            {{-- Form nhập --}}
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-5 border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                    <i class="ri-pencil-fill text-indigo-500"></i> Nhập chỉ số mới
                </h2>

                <form id="formAddChiSo" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    {{-- Phòng --}}
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Phòng</label>
                        <select id="phong_id" name="phong_id"
                            class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-400">
                            <option value="">-- Chọn phòng --</option>
                        </select>
                    </div>

                    {{-- Đồng hồ --}}
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Đồng hồ</label>
                        <select id="dong_ho_id" name="dong_ho_id"
                            class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-400">
                            <option value="">-- Chọn đồng hồ --</option>
                        </select>
                    </div>

                    {{-- Chỉ số mới --}}
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Chỉ số mới</label>
                        <input type="number" id="gia_tri" name="gia_tri"
                            class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-400"
                            placeholder="VD: 150">
                    </div>

                    {{-- Ngày cập nhật --}}
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Ngày cập nhật</label>
                        <input type="date" id="thoi_gian" name="thoi_gian"
                            class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-400">
                    </div>

                    {{-- Ghi chú --}}
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Ghi chú</label>
                        <input type="text" id="ghi_chu" name="ghi_chu"
                            class="w-full border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-400"
                            placeholder="VD: tháng 10">
                    </div>

                    <div class="col-span-1 md:col-span-5 text-right mt-2">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-xl font-medium transition flex items-center justify-center gap-1 mx-auto md:mx-0">
                            <i class="ri-save-3-line"></i> Lưu chỉ số
                        </button>
                    </div>
                </form>
            </div>

            {{-- Bộ lọc --}}
            <div class="bg-white shadow-sm rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="ri-filter-2-line text-indigo-500"></i>
                    <h3 class="font-medium text-gray-700">Bộ lọc</h3>
                </div>
                <div class="flex gap-3">
                    <select id="filterPhong" class="border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-400 text-sm">
                        <option value="">Tất cả phòng</option>
                    </select>
                    <button id="btnRefresh"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm text-gray-700 flex items-center gap-1">
                        <i class="ri-refresh-line"></i> Làm mới
                    </button>
                </div>
            </div>

            {{-- Lịch sử --}}
            <div class="bg-white shadow-md rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="ri-history-line text-blue-500"></i> Bảng chỉ số
                </h2>

                <div class="overflow-x-auto">
                    <table
                        class="min-w-full text-sm text-left text-gray-600 border border-gray-100 rounded-xl overflow-hidden">
                        <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-2">Phòng</th>
                                <th class="px-4 py-2">Loại đồng hồ</th>
                                <th class="px-4 py-2">Chỉ số cũ</th>
                                <th class="px-4 py-2">Chỉ số mới</th>
                                <th class="px-4 py-2">Ngày cập nhật</th>
                                <th class="px-4 py-2">Ghi chú</th>
                                <th class="px-4 py-2 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="tableChiSo" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal sửa chỉ số --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-lg p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="ri-edit-2-line text-yellow-500"></i> Sửa chỉ số
            </h3>

            <form id="formEditChiSo" class="space-y-3">
                <input type="hidden" id="edit_id">
                <div>
                    <label class="text-sm text-gray-500">Phòng</label>
                    <input type="text" id="edit_phong" disabled
                        class="w-full border-gray-200 rounded-xl bg-gray-100 text-gray-600 px-3 py-2">
                </div>

                <div>
                    <label class="text-sm text-gray-500">Chỉ số mới</label>
                    <input type="number" id="edit_gia_tri"
                        class="w-full border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-400">
                </div>

                <div>
                    <label class="text-sm text-gray-500">Ghi chú</label>
                    <input type="text" id="edit_ghi_chu"
                        class="w-full border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-400">
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" id="btnCancelEdit"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition">
                        Hủy
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition flex items-center gap-1">
                        <i class="ri-check-line"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiUrl = 'http://127.0.0.1:8000/api/chu-tro';
            const token = localStorage.getItem("token");
            if (!token) {
                alert("Bạn chưa đăng nhập! Vui lòng đăng nhập lại.");
                window.location.href = "http://127.0.0.1:8001/login";
                return;
            }

            const form = document.querySelector('#formAddChiSo');
            const phongSelect = document.querySelector('#phong_id');
            const dongHoSelect = document.querySelector('#dong_ho_id');
            const tableBody = document.querySelector('#tableChiSo');
            const filterPhong = document.querySelector('#filterPhong');
            const btnRefresh = document.querySelector('#btnRefresh');
            const modal = document.querySelector('#editModal');
            const btnCancel = document.querySelector('#btnCancelEdit');
            const formEdit = document.querySelector('#formEditChiSo');

            fetch(apiUrl + '/danh-sach-phong-dang-su-dung', {
                headers: { "Authorization": "Bearer " + token, "Accept": "application/json" }
            })
                .then(res => res.json())
                .then(phongs => {
                    phongSelect.innerHTML = '<option value="">-- Chọn phòng --</option>';
                    filterPhong.innerHTML = '<option value="">Tất cả phòng</option>';
                    phongs.forEach(p => {
                        const tenDay = p.day_tro?.ten_day_tro ?? 'Không rõ dãy';
                        phongSelect.innerHTML += `<option value="${p.id}">${tenDay} - ${p.so_phong}</option>`;
                        filterPhong.innerHTML += `<option value="${p.so_phong}">${p.so_phong}</option>`;
                    });
                });

            phongSelect.addEventListener('change', async e => {
                const phongId = e.target.value;
                dongHoSelect.innerHTML = '<option value="">-- Chọn đồng hồ --</option>';
                if (!phongId) return;
                const res = await fetch(`${apiUrl}/dong-ho?phong_id=${phongId}`, {
                    headers: { "Authorization": "Bearer " + token, "Accept": "application/json" }
                });
                const data = await res.json();
                data.forEach(dh => {
                    const opt = document.createElement('option');
                    opt.value = dh.id;
                    opt.textContent = `${dh.ma_dong_ho} (${dh.dich_vu?.ten ?? 'Chưa rõ'})`;
                    dongHoSelect.appendChild(opt);
                });
            });

            function loadChiSo(phongFilter = '') {
                fetch(apiUrl + '/chi-so', {
                    headers: { "Authorization": "Bearer " + token, "Accept": "application/json" }
                })
                    .then(res => res.json())
                    .then(data => {
                        const filtered = phongFilter ? data.filter(cs => cs.phong === phongFilter) : data;
                        tableBody.innerHTML = filtered.map(cs => `
                                                                            <tr class="hover:bg-gray-50 transition">
                                                                                <td class="px-4 py-2 font-medium">${cs.phong}</td>
                                                                                <td class="px-4 py-2">${cs.dich_vu}</td>
                                                                                <td class="px-4 py-2 text-gray-400">${cs.chi_so_cu ?? '0'}</td>
                                                                                <td class="px-4 py-2 text-indigo-600 font-semibold">${cs.chi_so_moi ?? '0'}</td>
                                                                                <td class="px-4 py-2">${cs.thoi_gian ?? '-'}</td>
                                                                                <td class="px-4 py-2 text-gray-500">${cs.ghi_chu ?? ''}</td>
                                                                                <td class="px-4 py-2 text-center">
                ${cs.trang_thai_hoa_don === 'da_thanh_toan'
                                ? `<span class="text-gray-400 text-xs italic">Đã thanh toán</span>`
                                : `<button onclick="editChiSo(${cs.id}, ${cs.chi_so_moi ?? 0}, '${cs.ghi_chu ?? ''}', '${cs.phong}')"
                        class="px-3 py-1 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg">
                        <i class="ri-edit-2-line"></i> Sửa
                       </button>`
                            }
            </td>

                                                                            </tr>
                                                                        `).join('');
                    });
            }

            loadChiSo();
            filterPhong.addEventListener('change', e => loadChiSo(e.target.value));
            btnRefresh.addEventListener('click', () => { filterPhong.value = ''; loadChiSo(); });

            window.editChiSo = function (id, giaTri, ghiChu, phong) {
                modal.classList.remove('hidden');
                document.querySelector('#edit_id').value = id;
                document.querySelector('#edit_phong').value = phong;
                document.querySelector('#edit_gia_tri').value = giaTri;
                document.querySelector('#edit_ghi_chu').value = ghiChu || '';
            };

            btnCancel.addEventListener('click', () => modal.classList.add('hidden'));

            formEdit.addEventListener('submit', e => {
                e.preventDefault();
                const id = document.querySelector('#edit_id').value;
                const giaTri = document.querySelector('#edit_gia_tri').value;
                const ghiChu = document.querySelector('#edit_ghi_chu').value;
                fetch(`${apiUrl}/chi-so/${id}`, {
                    method: "PUT",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ gia_tri: giaTri, ghi_chu: ghiChu })
                })
                    .then(res => res.json())
                    .then(res => {
                        alert(res.message || 'Cập nhật thành công!');
                        modal.classList.add('hidden');
                        loadChiSo();
                    })
                    .catch(err => {
                        console.error("Lỗi khi cập nhật:", err);
                        alert("❌ Không thể cập nhật chỉ số!");
                    });
            });

            form.addEventListener('submit', e => {
                e.preventDefault();
                const formData = new FormData(form);
                fetch(apiUrl + '/chi-so', {
                    method: 'POST',
                    headers: { "Authorization": "Bearer " + token, "Accept": "application/json" },
                    body: formData
                })
                    .then(res => res.json())
                    .then(res => {
                        alert(res.message || 'Lưu thành công!');
                        form.reset();
                        loadChiSo();
                    });
            });
        });
    </script>
@endsection