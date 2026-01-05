@extends('layouts.chu-tro')

@section('title', 'Cập nhật dịch vụ')

@section('content')
    <div class="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-lg">
        <h3 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">✏️ Cập nhật dịch vụ</h3>

        <form id="updateForm" action="{{ route('chu-tro.dich-vu.update', $dich_vu->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã dịch vụ</label>
                <input type="text" name="ma" value="{{ $dich_vu->ma }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên dịch vụ</label>
                <input type="text" name="ten" value="{{ $dich_vu->ten }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị</label>
                <input type="text" name="don_vi" value="{{ $dich_vu->don_vi }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn giá (VNĐ)</label>
                <input type="number" name="don_gia" value="{{ $dich_vu->don_gia }}" required min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="co_dong_ho" id="co_dong_ho" value="1"
                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500" {{ $dich_vu->co_dong_ho ? 'checked' : '' }}>
                <label for="co_dong_ho" class="ml-2 text-sm text-gray-700">Dịch vụ có đồng hồ</label>
            </div>

            <div class="flex justify-between pt-4">
                <button id="submitBtn" type="submit"
                    class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">💾 Cập
                    nhật</button>
                <a href="{{ route('chu-tro.dich-vu.index') }}"
                    class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">⬅️ Quay lại</a>
            </div>
        </form>
    </div>

    {{-- ✅ SweetAlert xử lý cập nhật --}}
    <script>
        document.getElementById('updateForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = this;
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = "⏳ Đang lưu..."; // 🌀 hiệu ứng nút

            const formData = new FormData(form);
            formData.append('_method', 'PUT'); // Laravel hiểu là PUT

            Swal.fire({
                title: 'Đang cập nhật...',
                text: 'Vui lòng chờ trong giây lát',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch(form.action, {
                    method: 'POST', // gửi POST + _method=PUT
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: result.message,
                        confirmButtonColor: '#16a34a'
                    }).then(() => window.location.href = "{{ route('chu-tro.dich-vu.index') }}");
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: result.message || 'Không thể cập nhật dịch vụ. Vui lòng kiểm tra lại dữ liệu.',
                        confirmButtonColor: '#9333ea'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi mạng!',
                    text: 'Không thể kết nối tới máy chủ. Vui lòng thử lại sau.',
                    confirmButtonColor: '#9333ea'
                });
                console.error(error);
            } finally {
                btn.disabled = false;
                btn.textContent = "💾 Cập nhật"; // 🔁 khôi phục nút
            }
        });
    </script>
@endsection