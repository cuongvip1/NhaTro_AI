@extends('layouts.chu-tro')

@section('title', 'Thêm dịch vụ')

@section('content')
    <div class="max-w-3xl mx-auto mt-10 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg">
        <h3 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100 flex items-center gap-2">
            ➕ Thêm dịch vụ mới
        </h3>

        <form id="formDichVu" action="{{ route('chu-tro.dich-vu.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- 🔹 Thông báo mã tự động --}}
            <div class="bg-blue-50 border border-blue-300 text-blue-700 text-sm p-3 rounded-lg">
                <i class="ri-information-line text-blue-500"></i>
                Mã dịch vụ sẽ được <strong>tự động sinh</strong> (ví dụ: DV001, DV002...).
            </div>

            {{-- Tên dịch vụ --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên dịch vụ</label>
                <input type="text" name="ten"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Ví dụ: Điện, Nước, Internet..." required>
            </div>

            {{-- Đơn vị --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Đơn vị</label>
                <input type="text" name="don_vi" id="don_vi_input"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="vd: kWh, m³, tháng..." required>
                <p id="ghi_chu_don_vi" class="text-xs text-gray-500 mt-1 italic"></p>
            </div>

            {{-- Đơn giá --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Đơn giá (VNĐ)</label>
                <input type="number" name="don_gia" min="0"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Nhập đơn giá" required>
            </div>

            {{-- Có đồng hồ --}}
            <div class="flex items-center">
                <input type="checkbox" name="co_dong_ho" value="1" id="co_dong_ho"
                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                <label for="co_dong_ho" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    Dịch vụ có đồng hồ đo (ví dụ: điện, nước)
                </label>
            </div>

            {{-- Nút hành động --}}
            <div class="flex justify-between pt-6 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                    class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow transition duration-150 ease-in-out">
                    💾 Lưu dịch vụ
                </button>
                <a href="{{ route('chu-tro.dich-vu.index') }}"
                    class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 hover:dark:bg-gray-600 transition duration-150 ease-in-out">
                    ⬅️ Quay lại
                </a>
            </div>
        </form>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('don_vi_input');
            const note = document.getElementById('ghi_chu_don_vi');
            const hints = {
                'm3': 'Mét khối — thường dùng cho nước',
                'kwh': 'Kilowatt giờ — thường dùng cho điện',
                'tháng': 'Phí tính theo tháng cố định',
                'người': 'Phí tính theo số người ở'
            };

            function updateHint() {
                const val = input.value.trim().toLowerCase();
                if (hints[val]) {
                    note.textContent = hints[val];
                    note.className = "text-xs text-green-600 mt-1 italic";
                } else if (val) {
                    note.textContent = 'Đơn vị tùy chọn (gợi ý: m³, kWh, tháng, người...)';
                    note.className = "text-xs text-gray-500 mt-1 italic";
                } else {
                    note.textContent = '';
                }
            }

            input.addEventListener('input', updateHint);

            // 🧩 SweetAlert sau khi submit
            const form = document.getElementById('formDichVu');
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Dịch vụ mới đã được thêm vào hệ thống.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#16a34a'
                        }).then(() => {
                            window.location.href = "{{ route('chu-tro.dich-vu.index') }}";
                        });
                    } else {
                        const err = await response.text();
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Không thể thêm dịch vụ. Vui lòng kiểm tra dữ liệu hoặc thử lại.',
                        });
                        console.error(err);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối!',
                        text: 'Vui lòng thử lại sau.',
                    });
                }
            });
        });
    </script>
@endsection