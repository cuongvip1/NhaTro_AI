@extends('layouts.chu-tro')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto space-y-6">
            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ri-bill-line text-indigo-500 text-3xl"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Quản lý hóa đơn</h1>
                </div>
                <a href="{{ route('chu-tro.dashboard') }}" class="text-sm text-indigo-600 hover:underline">← Quay lại</a>
            </div>

           <div
    class="bg-white shadow-sm rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 border border-gray-100">

            <div class="flex items-center gap-2">
                <i class="ri-filter-2-line text-indigo-500"></i>
                <h3 class="font-medium text-gray-700">Bộ lọc</h3>
            </div>

            <div class="flex flex-wrap gap-3">

                <!-- Lọc phòng -->
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-xl border border-gray-200">
                    <i class="ri-home-4-line text-gray-500 text-base"></i>
                    <select id="filterPhong" class="bg-transparent border-none focus:ring-0 text-sm">
                        <option value="">Tất cả phòng</option>
                    </select>
                </div>

                <!-- Lọc tháng -->
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-xl border border-gray-200">
                    <i class="ri-calendar-line text-gray-500 text-base"></i>
                    <input type="month" id="filterThang"
                        class="bg-transparent border-none focus:ring-0 text-sm" />
                </div>

                <!-- Lọc trạng thái -->
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-xl border border-gray-200">
                    <i class="ri-list-check text-gray-500 text-base"></i>
                    <select id="filterTrangThai" class="bg-transparent border-none focus:ring-0 text-sm">
                        <option value="">Tất cả trạng thái</option>
                        <option value="chua_thanh_toan">Chưa thanh toán</option>
                        <option value="mot_phan">Một phần</option>
                        <option value="cho_xac_nhan">Chờ xác nhận</option>
                        <option value="da_thanh_toan">Đã thanh toán</option>
                        <option value="da_huy">Đã hủy</option>
                    </select>
                </div>

                <!-- Làm mới -->
                <button id="btnRefresh"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm text-gray-700 flex items-center gap-1">
                    <i class="ri-refresh-line"></i> Làm mới
                </button>

                <!-- Gửi yêu cầu thanh toán tất cả -->
                <button id="btnSendAll"
                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm flex items-center gap-1">
                    <i class="ri-send-plane-fill"></i> Gửi yêu cầu thanh toán tất cả
                </button>

                <!-- Tạo hóa đơn -->
                <button id="btnGenerate"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm flex items-center gap-1">
                    <i class="ri-add-circle-line"></i> Tạo hóa đơn tháng này
                </button>

                <!-- Xuất Excel -->
                <button id="btnExportExcel"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm flex items-center gap-1">
                    <i class="ri-file-excel-2-line"></i> Xuất báo cáo doanh thu
                </button>

            </div>
        </div>


            {{-- Danh sách hóa đơn --}}
            <div class="bg-white shadow-md rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="ri-file-list-3-line text-blue-500"></i> Danh sách hóa đơn
                </h2>

                <div class="overflow-x-auto">
                    <table
                        class="min-w-full text-sm text-left text-gray-600 border border-gray-100 rounded-xl overflow-hidden">
                        <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-2">Phòng</th>
                                <th class="px-4 py-2">Tháng</th>
                                <th class="px-4 py-2 text-right">Tiền phòng</th>
                                <th class="px-4 py-2 text-right">Dịch vụ</th>
                                <th class="px-4 py-2 text-right">Điện nước</th>
                                <th class="px-4 py-2 text-right">Tổng tiền</th>
                                <th class="px-4 py-2">Hạn thanh toán</th>
                                <th class="px-4 py-2 text-center">Trạng thái</th>
                                <th class="px-4 py-2 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="tableHoaDon" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- chi tiết hóa đơn --}}
    <div id="modalDetail" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-6 relative animate-fade-in">
            <button id="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
                <i class="ri-close-line text-2xl"></i>
            </button>

            <div class="flex items-center gap-2 mb-4 border-b pb-2">
                <i class="ri-file-list-3-line text-indigo-500 text-2xl"></i>
                <h2 class="text-xl font-bold text-gray-800">Chi tiết hóa đơn</h2>
            </div>

            <div id="hoaDonInfo" class="grid grid-cols-2 gap-y-2 text-sm text-gray-700 mb-5"></div>
            <div id="chiTietDichVu" class="mb-5"></div>
            <div id="chiTietDongHo" class="mb-5"></div>

            <div class="flex justify-between items-center border-t pt-4 mt-4">
                <div class="flex gap-3">
                    <button id="btnPrintPdf"
                        class="flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-700 hover:bg-indigo-200 rounded-xl text-sm transition">
                        <i class="ri-printer-line"></i> In PDF
                    </button>
                    <button id="btnXacNhanModal"
                        class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 hover:bg-green-200 rounded-xl text-sm transition">
                        <i class="ri-check-double-line"></i> Xác nhận thanh toán
                    </button>
                </div>
                <div id="tongTien" class="text-lg font-bold text-indigo-600"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: scale(0.98);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.2s ease-in-out;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const apiUrl = 'http://127.0.0.1:8000/api/chu-tro';
        const exportExcelUrl = "{{ route('chu-tro.hoa-don.export') }}";
        const token = localStorage.getItem('token');
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.querySelector('#tableHoaDon');
            const btnGenerate = document.querySelector('#btnGenerate');
            const filterPhong = document.querySelector('#filterPhong');
            const btnRefresh = document.querySelector('#btnRefresh');
            const filterThang = document.querySelector('#filterThang'); 
            const filterTrangThai = document.querySelector('#filterTrangThai');
            const btnExportExcel = document.querySelector('#btnExportExcel');

            if (!token) {
                alert('Bạn chưa đăng nhập!');
                window.location.href = 'http://127.0.0.1:8001/login';
                return;
            }

            function showAlert(type, message) {
                Swal.fire({
                    icon: type,
                    title: message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4f46e5',
                    timer: 2500,
                    timerProgressBar: true
                });
            }

            async function loadPhongOptions() {
                const res = await fetch(`${apiUrl}/phong`, { headers: { 'Authorization': 'Bearer ' + token } });
                const data = await res.json();
                const validRooms = data.filter(p => ['da_thue', 'dang_thue'].includes(p.trang_thai));
                filterPhong.innerHTML = '<option value="">Tất cả phòng</option>' +
                    validRooms.map(p => `<option value="${p.so_phong}">${p.so_phong}</option>`).join('');
            }

            /*async function loadHoaDon(phongFilter = '') {
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-gray-400">Đang tải...</td></tr>';

                const res = await fetch(`${apiUrl}/hoa-don`, { headers: { 'Authorization': 'Bearer ' + token } });
                const data = await res.json();

                const filtered = phongFilter ? data.filter(hd => hd.hop_dong?.phong?.so_phong === phongFilter) : data;

                const trangThaiText = {
                    da_thanh_toan: hd => `<span class="px-2 py-1 bg-green-100 text-green-600 rounded-lg text-xs font-medium">
                                                                                        Đã thanh toán (${Number(hd.tong_tien ?? 0).toLocaleString('vi-VN')} đ)</span>`,
                    mot_phan: hd => `<span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-lg text-xs font-medium">
                                                                                        Một phần (${Number(hd.so_tien_da_tra ?? 0).toLocaleString('vi-VN')} / ${Number(hd.tong_tien ?? 0).toLocaleString('vi-VN')} đ)</span>`,
                    chua_thanh_toan: () => `<span class="px-2 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-medium">Chưa thanh toán</span>`,
                    cho_xac_nhan: () => `<span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-medium">Chờ xác nhận</span>`, // 🆕 THÊM MỚI
                    da_huy: () => `<span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-lg text-xs font-medium">Đã hủy</span>`
                };


                tableBody.innerHTML = filtered.length
                    ? filtered.map(hd => {
                        const phong = hd.hop_dong?.phong?.so_phong ?? 'N/A';
                        const thang = hd.thang ?? '-';
                        const tienPhong = Number(hd.tien_phong ?? 0).toLocaleString('vi-VN') + ' đ';
                        const tienDichVu = Number(hd.tien_dich_vu ?? 0).toLocaleString('vi-VN') + ' đ';
                        const tienDongHo = Number(hd.tien_dong_ho ?? 0).toLocaleString('vi-VN') + ' đ';
                        const tongTien = Number(hd.tong_tien ?? 0).toLocaleString('vi-VN') + ' đ';
                        const han = hd.han_thanh_toan ? new Date(hd.han_thanh_toan).toLocaleDateString('vi-VN') : '-';
                        const rowClass = hd.trang_thai === 'da_huy' ? 'opacity-50 bg-gray-50' : hd.qua_han ? 'bg-red-50' : '';

                        const btnXem = `<button onclick="viewDetail(${hd.id})"
                                                                                                                                                                    class="px-3 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg">
                                                                                                                                                                    <i class='ri-eye-line'></i> Xem</button>`;

                        let btnThanhToan = '';

                        if (hd.trang_thai === 'cho_xac_nhan') {
                            btnThanhToan = `<button onclick="xacNhanThanhToan(${hd.id})"
                                        class="px-3 py-1 text-xs bg-green-100 hover:bg-green-200 text-green-700 rounded-lg">
                                        <i class='ri-check-double-line'></i> Xác nhận</button>`;
                        } else if (hd.trang_thai !== 'da_thanh_toan' && hd.trang_thai !== 'da_huy') {
                            btnThanhToan = `<button onclick="guiYeuCauThanhToan(${hd.id})"
        class="px-3 py-1 text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg">
        <i class='ri-notification-3-line'></i> Gửi yêu cầu</button>`;
                        }



                        const btnHuy = hd.trang_thai !== 'da_thanh_toan' && hd.trang_thai !== 'da_huy'
                            ? `<button onclick="huyHoaDon(${hd.id})"
                                                                                                                                                                        class="px-3 py-1 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-lg">
                                                                                                                                                                        <i class='ri-close-line'></i> Hủy</button>` : '';

                        return `
                                                                                                                                                                    <tr class="hover:bg-gray-50 transition ${rowClass}">
                                                                                                                                                                        <td class="px-4 py-2 font-medium">${phong}</td>
                                                                                                                                                                        <td class="px-4 py-2">${thang}</td>
                                                                                                                                                                        <td class="px-4 py-2 text-right">${tienPhong}</td>
                                                                                                                                                                        <td class="px-4 py-2 text-right">${tienDichVu}</td>
                                                                                                                                                                        <td class="px-4 py-2 text-right">${tienDongHo}</td>
                                                                                                                                                                        <td class="px-4 py-2 text-right font-semibold text-indigo-600">${tongTien}</td>
                                                                                                                                                                        <td class="px-4 py-2">${han}</td>
                                                                                                                                                                        <td class="px-4 py-2 text-center">${trangThaiText[hd.trang_thai]?.(hd) || ''}</td>
                                                                                                                                                                        <td class="px-4 py-2 text-center flex justify-center gap-2">${btnXem}${btnThanhToan}${btnHuy}</td>
                                                                                                                                                                    </tr>`;
                    }).join('')
                    : `<tr><td colspan="9" class="text-center py-4 text-gray-400">Không có hóa đơn nào</td></tr>`;
            }*/

            async function loadHoaDon(phongFilter = '', thangFilter = '', trangThaiFilter = '') {
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-gray-400">Đang tải...</td></tr>';

                const params = new URLSearchParams();
                if (phongFilter) params.append('phong', phongFilter);
                //if (thangFilter) params.append('thang', thangFilter);
                if (thangFilter) {
                    thangFilter = thangFilter.substring(0, 7);
                    params.append('thang', thangFilter);
                }
                if (trangThaiFilter) params.append('trang_thai', trangThaiFilter);
                const res = await fetch(`${apiUrl}/hoa-don?${params.toString()}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();

                const trangThaiText = {
                    da_thanh_toan: hd => `<span class="px-2 py-1 bg-green-100 text-green-600 rounded-lg text-xs font-medium">
                        Đã thanh toán (${Number(hd.tong_tien ?? 0).toLocaleString('vi-VN')} đ)</span>`,
                    mot_phan: hd => `<span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-lg text-xs font-medium">
                        Một phần (${Number(hd.so_tien_da_tra ?? 0).toLocaleString('vi-VN')} / ${Number(hd.tong_tien ?? 0).toLocaleString('vi-VN')} đ)</span>`,
                    chua_thanh_toan: () => `<span class="px-2 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-medium">Chưa thanh toán</span>`,
                    cho_xac_nhan: () => `<span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-medium">Chờ xác nhận</span>`,
                    da_huy: () => `<span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-lg text-xs font-medium">Đã hủy</span>`
                };

                const list = data;

                tableBody.innerHTML = list.length
                    ? list.map(hd => {
                        const phong = hd.hop_dong?.phong?.so_phong ?? 'N/A';
                        const thang = hd.thang ?? '-';
                        const tienPhong = Number(hd.tien_phong ?? 0).toLocaleString('vi-VN') + ' đ';
                        const tienDichVu = Number(hd.tien_dich_vu ?? 0).toLocaleString('vi-VN') + ' đ';
                        const tienDongHo = Number(hd.tien_dong_ho ?? 0).toLocaleString('vi-VN') + ' đ';
                        const tongTien = Number(hd.tong_tien ?? 0).toLocaleString('vi-VN') + ' đ';
                        const han = hd.han_thanh_toan ? new Date(hd.han_thanh_toan).toLocaleDateString('vi-VN') : '-';
                        const rowClass = hd.trang_thai === 'da_huy'
                            ? 'opacity-50 bg-gray-50'
                            : hd.qua_han ? 'bg-red-50' : '';

                        const btnXem = `<button onclick="viewDetail(${hd.id})"
                            class="px-3 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg">
                            <i class='ri-eye-line'></i> Xem</button>`;

                        let btnThanhToan = '';
                        if (hd.trang_thai === 'cho_xac_nhan') {
                            btnThanhToan = `<button onclick="xacNhanThanhToan(${hd.id})"
                                class="px-3 py-1 text-xs bg-green-100 hover:bg-green-200 text-green-700 rounded-lg">
                                <i class='ri-check-double-line'></i> Xác nhận</button>`;
                        } else if (hd.trang_thai !== 'da_thanh_toan' && hd.trang_thai !== 'da_huy') {
                            btnThanhToan = `<button onclick="guiYeuCauThanhToan(${hd.id})"
                                class="px-3 py-1 text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg">
                                <i class='ri-notification-3-line'></i> Gửi yêu cầu</button>`;
                        }

                        const btnHuy = hd.trang_thai !== 'da_thanh_toan' && hd.trang_thai !== 'da_huy'
                            ? `<button onclick="huyHoaDon(${hd.id})"
                                class="px-3 py-1 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-lg">
                                <i class='ri-close-line'></i> Hủy</button>`
                            : '';

                        return `
                            <tr class="hover:bg-gray-50 transition ${rowClass}">
                                <td class="px-4 py-2 font-medium">${phong}</td>
                                <td class="px-4 py-2">${thang}</td>
                                <td class="px-4 py-2 text-right">${tienPhong}</td>
                                <td class="px-4 py-2 text-right">${tienDichVu}</td>
                                <td class="px-4 py-2 text-right">${tienDongHo}</td>
                                <td class="px-4 py-2 text-right font-semibold text-indigo-600">${tongTien}</td>
                                <td class="px-4 py-2">${han}</td>
                                <td class="px-4 py-2 text-center">${trangThaiText[hd.trang_thai]?.(hd) || ''}</td>
                                <td class="px-4 py-2 text-center flex justify-center gap-2">${btnXem}${btnThanhToan}${btnHuy}</td>
                            </tr>`;
                    }).join('')
                    : `<tr><td colspan="9" class="text-center py-4 text-gray-400">Không có hóa đơn nào</td></tr>`;
            }
            filterTrangThai.addEventListener('change', () => {
                let v = filterThang.value;
                if (v && v.length >= 7) v = v.substring(0, 7);
                loadHoaDon(filterPhong.value, v, filterTrangThai.value);
            });

            filterPhong.addEventListener('change', () => {
                let v = filterThang.value;
                if (v && v.length >= 7) v = v.substring(0, 7);
                loadHoaDon(filterPhong.value, v, filterTrangThai.value);
            });

            filterThang.addEventListener('change', () => {
                let v = filterThang.value;
                if (v && v.length >= 7) {
                    v = v.substring(0, 7);
                }
                loadHoaDon(filterPhong.value, v, filterTrangThai.value);
            });

            btnRefresh.addEventListener('click', () => {
                filterPhong.value = '';
                filterThang.value = '';
                filterTrangThai.value = '';
                loadHoaDon();
            });

            if (btnExportExcel) {
                btnExportExcel.addEventListener('click', () => {
                    const url = new URL(exportExcelUrl, window.location.origin);
                    if (filterPhong.value) url.searchParams.set('phong', filterPhong.value);
                    const thangValue = (filterThang.value || '').substring(0, 7);
                    if (thangValue) url.searchParams.set('thang', thangValue);
                    if (filterTrangThai.value) url.searchParams.set('trang_thai', filterTrangThai.value);
                    window.location.href = url.toString();
                });
            }

            window.thanhToan = async (id) => {
                const res = await fetch(`${apiUrl}/hoa-don/${id}/thanh-toan`, {
                    method: 'POST', headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                showAlert(data.success ? 'success' : 'error',
                    data.success ? '💰 Cập nhật: ' + data.trang_thai : (data.message || 'Không thể cập nhật.'));
                loadHoaDon();
            };
            window.guiYeuCauThanhToan = async (id) => {
                const confirmResult = await Swal.fire({
                    title: 'Gửi yêu cầu thanh toán?',
                    text: 'Khách thuê sẽ nhận được thông báo cần thanh toán hóa đơn này.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Gửi ngay',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#4f46e5'
                });
                if (!confirmResult.isConfirmed) return;

                try {
                    const res = await fetch(`${apiUrl}/hoa-don/${id}/yeu-cau-thanh-toan`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await res.json();

                    if (res.ok && data.success) {
                        showAlert('success', '📨 Đã gửi yêu cầu thanh toán cho khách!');
                    } else {
                        showAlert('error', data.message || 'Không thể gửi yêu cầu.');
                    }
                } catch (err) {
                    console.error('Lỗi gửi yêu cầu thanh toán:', err);
                    showAlert('error', 'Không thể kết nối đến máy chủ.');
                }
            };

            window.xacNhanThanhToan = async (id) => {
                const confirmResult = await Swal.fire({
                    title: 'Xác nhận khách đã thanh toán?',
                    text: 'Hóa đơn này sẽ được đánh dấu là ĐÃ THANH TOÁN.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#16a34a',
                });

                if (!confirmResult.isConfirmed) return;

                try {
                    const res = await fetch(`${apiUrl}/hoa-don/${id}/xac-nhan-thanh-toan`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    });

                    let data = null;
                    try {
                        data = await res.json();
                    } catch (err) {
                        console.warn('Không parse được JSON từ server:', err);
                    }

                    if (res.ok && data?.success) {
                        showAlert('success', data.message || 'Đã xác nhận thanh toán!');
                        loadHoaDon();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Lỗi!',
                            text: data?.message || `Yêu cầu không hợp lệ (mã ${res.status})`,
                            confirmButtonColor: '#f59e0b'
                        });
                    }

                } catch (err) {
                    console.error('Lỗi fetch xác nhận thanh toán:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Không thể kết nối đến máy chủ!',
                        text: 'Kiểm tra lại API backend hoặc token đăng nhập.'
                    });
                }
            };

            window.huyXacNhan = async (id) => {
                const confirmResult = await Swal.fire({
                    title: 'Huỷ xác nhận thanh toán?',
                    text: 'Hóa đơn sẽ quay về trạng thái CHƯA THANH TOÁN để khách có thể thanh toán lại.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Huỷ xác nhận',
                    cancelButtonText: 'Đóng',
                    confirmButtonColor: '#e11d48'
                });
                if (!confirmResult.isConfirmed) return;

                try {
                    const res = await fetch(`${apiUrl}/hoa-don/${id}/huy-xac-nhan`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    if (res.ok && data.success) {
                        Swal.fire('Thành công', data.message || 'Đã huỷ xác nhận.', 'success');
                        document.getElementById('modalDetail').classList.add('hidden');
                        if (typeof loadHoaDon === 'function') loadHoaDon();
                    } else {
                        Swal.fire(' Lỗi', data.message || `Không thể huỷ xác nhận (mã ${res.status})`, 'error');
                    }
                } catch (err) {
                    console.error(' Lỗi fetch huỷ xác nhận:', err);
                    Swal.fire(' Lỗi', 'Không thể kết nối đến máy chủ.', 'error');
                }
            };

            window.huyHoaDon = async (id) => {
                const confirmResult = await Swal.fire({
                    title: 'Bạn có chắc muốn hủy hóa đơn này?',
                    text: 'Thao tác này không thể hoàn tác!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Vâng, hủy ngay',
                    cancelButtonText: 'Không',
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#6b7280'
                });
                if (!confirmResult.isConfirmed) return;

                try {
                    const res = await fetch(`${apiUrl}/hoa-don/${id}/huy`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    });

                    const data = await res.json();
                    if (data.success) showAlert('success', data.message || 'Đã hủy hóa đơn thành công!');
                    else showAlert('error', data.message || 'Không thể hủy hóa đơn!');
                    loadHoaDon();
                } catch (err) {
                    console.error('❌ Lỗi khi hủy hóa đơn:', err);
                    showAlert('error', 'Không thể kết nối đến máy chủ.');
                }
            };

            window.viewDetail = async (id) => {
                const modal = document.getElementById('modalDetail');
                const info = document.getElementById('hoaDonInfo');
                const dv = document.getElementById('chiTietDichVu');
                const dh = document.getElementById('chiTietDongHo');
                const tong = document.getElementById('tongTien');

                try {
                    window.currentHoaDonId = id;

                    const res = await fetch(`${apiUrl}/hoa-don/${id}`, {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();
                    console.log('📦 Dữ liệu hóa đơn:', data);

                    if (!res.ok || data.error) {
                        console.error('❌ Lỗi tải chi tiết hóa đơn:', data.error || res.status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Không thể tải chi tiết hóa đơn!',
                            text: data.error || 'Vui lòng thử lại sau.'
                        });
                        return;
                    }

                    const statusLabel = {
                        da_thanh_toan: '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-medium">Đã thanh toán</span>',
                        mot_phan: '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-medium">Thanh toán một phần</span>',
                        chua_thanh_toan: '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-medium">Chưa thanh toán</span>',
                        da_huy: '<span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-lg text-xs font-medium">Đã hủy</span>'
                    };

                   info.innerHTML = `
    <div><b>Phòng:</b> ${data.phong ?? 'N/A'}</div>
    <div><b>Dãy trọ:</b> ${data.day_tro ?? 'N/A'}</div>
    <div><b>Tháng:</b> ${data.thang ?? '-'}</div>
    <div><b>Trạng thái:</b> ${statusLabel[data.trang_thai] ?? data.trang_thai}</div>
    <div><b>Hạn thanh toán:</b> ${data.han_thanh_toan ?? '-'}</div>

    <hr class="col-span-2 my-2 border-gray-200">

    <div><b>Chủ trọ:</b> ${data.chu_tro?.ho_ten ?? 'Chưa cập nhật'}</div>
    <div><b>SĐT Chủ trọ:</b> ${data.chu_tro?.so_dien_thoai ?? 'Chưa cập nhật'}</div>

    <div><b>Người thuê:</b> ${data.khach_thue?.ho_ten ?? 'Chưa cập nhật'}</div>
    <div><b>SĐT Người thuê:</b> ${data.khach_thue?.so_dien_thoai ?? 'Chưa cập nhật'}</div>
`;



                    dv.innerHTML = data.chi_tiet_dich_vu?.length
                        ? `
                                                                                                                                                        <h3 class="text-base font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                                                                                                                                            <i class="ri-service-line text-indigo-500"></i> Dịch vụ
                                                                                                                                                        </h3>
                                                                                                                                                        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                                                                                                                                            <thead class="bg-gray-50 text-gray-700 font-medium">
                                                                                                                                                                <tr>
                                                                                                                                                                    <th class="p-2 text-left">Tên</th>
                                                                                                                                                                    <th class="p-2 text-right">Số lượng</th>
                                                                                                                                                                    <th class="p-2 text-right">Đơn giá</th>
                                                                                                                                                                    <th class="p-2 text-right">Thành tiền</th>
                                                                                                                                                                </tr>
                                                                                                                                                            </thead>
                                                                                                                                                            <tbody>
                                                                                                                                                                ${data.chi_tiet_dich_vu.map(i => `
                                                                                                                                                                    <tr>
                                                                                                                                                                        <td class="p-2">${i.ten_dich_vu ?? '-'}</td>
                                                                                                                                                                        <td class="p-2 text-right">${i.so_luong ?? 1}</td>
                                                                                                                                                                        <td class="p-2 text-right">${Number(i.don_gia ?? 0).toLocaleString('vi-VN')} đ</td>
                                                                                                                                                                        <td class="p-2 text-right font-medium text-gray-700">${Number(i.thanh_tien ?? 0).toLocaleString('vi-VN')} đ</td>
                                                                                                                                                                    </tr>
                                                                                                                                                                `).join('')}
                                                                                                                                                            </tbody>
                                                                                                                                                        </table>`
                        : `<div class="text-gray-400 text-sm italic">Không có dịch vụ định kỳ</div>`;

                    const chiTietDienNuoc = data.chi_tiet_dong_ho || data.chi_tiet_dien_nuoc || [];
                    dh.innerHTML = chiTietDienNuoc.length
                        ? `
                                                                                                                                                        <h3 class="text-base font-semibold text-gray-800 mt-5 mb-2 flex items-center gap-2">
                                                                                                                                                            <i class="ri-flashlight-line text-indigo-500"></i> Điện nước
                                                                                                                                                        </h3>
                                                                                                                                                        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                                                                                                                                            <thead class="bg-gray-50 text-gray-700 font-medium">
                                                                                                                                                                <tr>
                                                                                                                                                                    <th class="p-2 text-left">Loại</th>
                                                                                                                                                                    <th class="p-2 text-right">CS Cũ</th>
                                                                                                                                                                    <th class="p-2 text-right">CS Mới</th>
                                                                                                                                                                    <th class="p-2 text-right">Tiêu thụ</th>
                                                                                                                                                                    <th class="p-2 text-right">Đơn giá</th>
                                                                                                                                                                    <th class="p-2 text-right">Thành tiền</th>
                                                                                                                                                                </tr>
                                                                                                                                                            </thead>
                                                                                                                                                            <tbody>
                                                                                                                                                                ${chiTietDienNuoc.map(i => `
                                                                                                                                                                    <tr>
                                                                                                                                                                        <td class="p-2">${i.ten_dich_vu ?? 'Không xác định'}</td>
                                                                                                                                                                        <td class="p-2 text-right">${i.chi_so_cu ?? '-'}</td>
                                                                                                                                                                        <td class="p-2 text-right">${i.chi_so_moi ?? '-'}</td>
                                                                                                                                                                        <td class="p-2 text-right">${i.san_luong ?? '-'}</td>
                                                                                                                                                                        <td class="p-2 text-right">${Number(i.don_gia ?? 0).toLocaleString('vi-VN')} đ</td>
                                                                                                                                                                        <td class="p-2 text-right font-medium text-gray-700">${Number(i.thanh_tien ?? 0).toLocaleString('vi-VN')} đ</td>
                                                                                                                                                                    </tr>
                                                                                                                                                                `).join('')}
                                                                                                                                                            </tbody>
                                                                                                                                                        </table>`
                        : `<h3 class="text-base font-semibold text-gray-800 mt-5 mb-2 flex items-center gap-2">
                                                                                                                                                        <i class="ri-flashlight-line text-indigo-500"></i> Điện nước
                                                                                                                                                       </h3><div class="text-gray-400 text-sm italic">Chưa có chỉ số đầu kỳ – sẽ tính từ tháng sau
</div>`;

                    tong.innerHTML = `Tổng cộng: <span class="text-indigo-600 font-bold">${Number(data.tong_tien ?? 0).toLocaleString('vi-VN')} đ</span>`;

                    const btnBar = document.querySelector('#btnXacNhanModal').parentElement;
                    const oldCancel = document.getElementById('btnHuyXacNhan');
                    if (oldCancel) oldCancel.remove();

                    if (data.trang_thai === 'cho_xac_nhan') {
                        const cancelBtn = document.createElement('button');
                        cancelBtn.id = 'btnHuyXacNhan';
                        cancelBtn.className = 'flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-xl text-sm transition';
                        cancelBtn.innerHTML = `<i class="ri-close-line"></i> Huỷ xác nhận`;
                        cancelBtn.onclick = () => huyXacNhan(window.currentHoaDonId);
                        btnBar.appendChild(cancelBtn);
                    }
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } catch (err) {
                    console.error('❌ Lỗi khi tải chi tiết hóa đơn:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Không thể tải dữ liệu chi tiết.',
                        text: err.message || 'Lỗi không xác định.'
                    });
                }
            };

            document.getElementById('closeModal').addEventListener('click', () => {
                const modal = document.getElementById('modalDetail');
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex', 'opacity-0');
                }, 200);
            });

            document.getElementById('btnPrintPdf').addEventListener('click', async () => {
                const id = window.currentHoaDonId;
                if (!id) return alert('❌ Không xác định được hóa đơn để in!');

                try {
                    const res = await fetch(`${apiUrl}/hoa-don/${id}/pdf`, {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/pdf'
                        }
                    });

                    const contentType = res.headers.get('content-type') || '';

                    if (contentType.includes('application/pdf')) {
                        const blob = await res.blob();
                        const blobUrl = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = blobUrl;
                        a.download = `hoa_don_${id}.pdf`;
                        a.click();
                        window.URL.revokeObjectURL(blobUrl);
                        console.log('✅ PDF tải thành công!');
                        return;
                    }

                    let errMsg = 'Không thể tạo file PDF.';
                    try {
                        const err = await res.json();
                        errMsg = err.error || JSON.stringify(err);
                    } catch { }
                    alert('⚠️ ' + errMsg);
                } catch (err) {
                    console.error('Lỗi tải PDF:', err);
                }
            });

            loadPhongOptions();
            loadHoaDon();
            //filterPhong.addEventListener('change', e => loadHoaDon(e.target.value));
            //btnRefresh.addEventListener('click', () => { filterPhong.value = ''; loadHoaDon(); });

            btnGenerate.addEventListener('click', async () => {
                const confirmResult = await Swal.fire({
                    title: 'Tạo hóa đơn tháng này?',
                    text: 'Sẽ tạo cho tất cả các phòng đang thuê.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Tạo ngay',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#4f46e5'
                });
                if (!confirmResult.isConfirmed) return;

                btnGenerate.disabled = true;
                btnGenerate.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Đang tạo...';
                const res = await fetch(`${apiUrl}/hoa-don/generate`, {
                    method: 'POST', headers: { 'Authorization': 'Bearer ' + token }
                });
                const data = await res.json();
                showAlert('success', data.message || 'Tạo hóa đơn thành công!');
                btnGenerate.disabled = false;
                btnGenerate.innerHTML = '<i class="ri-add-circle-line"></i> Tạo hóa đơn tháng này';
                loadHoaDon();
            });
        });

        const btnSendAll = document.getElementById('btnSendAll');

        btnSendAll.addEventListener('click', async () => {
            const confirmResult = await Swal.fire({
                title: 'Gửi yêu cầu thanh toán cho TẤT CẢ phòng?',
                text: 'Tất cả khách chưa thanh toán sẽ nhận thông báo.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Gửi',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#f97316'
            });

            if (!confirmResult.isConfirmed) return;

            try {
                btnSendAll.disabled = true;
                btnSendAll.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Đang gửi...';

                const res = await fetch(`${apiUrl}/hoa-don/gui-yeu-cau-thanh-toan-all`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    Swal.fire('Thành công', data.message, 'success');
                } else {
                    Swal.fire('Thông báo', data.message || 'Không thể gửi.', 'warning');
                }

            } catch (err) {
                Swal.fire('Lỗi', 'Không kết nối được server.', 'error');
            } finally {
                btnSendAll.disabled = false;
                btnSendAll.innerHTML =
                    '<i class="ri-send-plane-fill"></i> Gửi yêu cầu thanh toán tất cả';
            }
        });


        const urlParams = new URLSearchParams(window.location.search);
        const idFromUrl = urlParams.get('id');
        if (idFromUrl) {
            setTimeout(() => {
                viewDetail(idFromUrl);
            }, 800);
        }
        document.getElementById('btnXacNhanModal').addEventListener('click', async () => {
            const id = window.currentHoaDonId;
            if (!id) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không xác định được hóa đơn.'
                });
            }

            const confirmResult = await Swal.fire({
                title: 'Xác nhận thanh toán hóa đơn?',
                text: 'Hóa đơn sẽ được chuyển sang trạng thái ĐÃ THANH TOÁN.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#16a34a'
            });

            if (!confirmResult.isConfirmed) return;

            try {
                const res = await fetch(`${apiUrl}/hoa-don/${id}/xac-nhan-thanh-toan`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                let data = null;
                try {
                    data = await res.json();
                } catch (err) {
                    console.warn('⚠️ Không parse được JSON:', err);
                    data = { success: res.ok };
                }

                if (res.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '✅ Thành công',
                        text: data.message || 'Đã xác nhận thanh toán.'
                    });

                    document.getElementById('modalDetail').classList.add('hidden');

                    // 🔧 kiểm tra và gọi loadHoaDon nếu có
                    if (typeof loadHoaDon === 'function') {
                        loadHoaDon();
                    } else {
                        console.warn('⚠️ loadHoaDon chưa được định nghĩa trong phạm vi hiện tại');
                    }
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Không thể xác nhận thanh toán!',
                        text: data.message || `Yêu cầu thất bại (mã ${res.status})`,
                        confirmButtonColor: '#f59e0b'
                    });
                }
            } catch (err) {
                console.error('❌ Lỗi fetch xác nhận thanh toán:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Không thể kết nối đến máy chủ!',
                    text: 'Kiểm tra lại API backend hoặc token đăng nhập.'
                });
            }
        });

    </script>


@endsection