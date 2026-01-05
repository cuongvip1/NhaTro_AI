@extends('layouts.chu-tro')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    <div class="max-w-4xl mx-auto py-10 px-6">
        <h1 class="text-3xl font-bold mb-8 text-indigo-600">👤 Hồ sơ cá nhân</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
            {{-- Reload trang để avatar trên header cập nhật --}}
            <script>
                setTimeout(() => location.reload(), 600);
            </script>
        @endif

        <form action="{{ route('chu-tro.profile.update') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg">
            @csrf

            <div class="flex items-center space-x-6">
                <img src="{{ $profile['anh_dai_dien'] ?? '/images/default-avatar.png' }}"
                    class="w-24 h-24 rounded-full object-cover shadow">
                <div>
                    <label class="block text-gray-600 dark:text-gray-300 font-medium mb-2">Ảnh đại diện</label>
                    <input type="file" name="anh_dai_dien" accept="image/*"
                        class="text-sm border rounded p-2 w-full bg-gray-50 dark:bg-gray-700">
                </div>
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 mb-1">Họ tên</label>
                <input type="text" name="ho_ten" value="{{ $profile['ho_ten'] ?? '' }}"
                    class="w-full border rounded p-3 bg-gray-50 dark:bg-gray-700">
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 mb-1">Email</label>
                <input type="email" value="{{ $profile['email'] ?? '' }}" readonly
                    class="w-full border rounded p-3 bg-gray-100 dark:bg-gray-700 text-gray-500">
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 mb-1">Số điện thoại</label>
                <input type="text" name="so_dien_thoai" value="{{ $profile['so_dien_thoai'] ?? '' }}"
                    class="w-full border rounded p-3 bg-gray-50 dark:bg-gray-700">
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 mb-1">Vai trò</label>
                <input type="text" value="{{ ucfirst(str_replace('_', ' ', $profile['vai_tro'] ?? '')) }}" readonly
                    class="w-full border rounded p-3 bg-gray-100 dark:bg-gray-700 text-gray-500">
            </div>

            <div class="pt-4 flex items-center space-x-4">
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow hover:bg-indigo-700 transition">
                    💾 Lưu thay đổi
                </button>

                {{-- 🔙 Nút quay lại --}}
                <a href="{{ route('chu-tro.dashboard') }}"
                    class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg shadow hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                    ← Quay lại
                </a>
            </div>
        </form>
    </div>

    @if (session('success'))
        <script>
            const headerImg = document.querySelector('header img[alt="Avatar"]');
            if (headerImg) {
                const url = new URL(headerImg.src, window.location.origin);
                url.searchParams.set('v', Date.now().toString());
                headerImg.src = url.toString();
            }
        </script>
    @endif
    {{-- 💳 THÔNG TIN NGÂN HÀNG --}}
    <div class="border-t pt-6 mt-6">
        <h2 class="text-xl font-semibold mb-4 text-indigo-600">💳 Thông tin ngân hàng</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-600 dark:text-gray-300 mb-1">Mã ngân hàng (VD: MB, VCB, ACB...)</label>
                <input type="text" id="bank_code" class="w-full border rounded p-3 bg-gray-50 dark:bg-gray-700"
                    placeholder="Nhập mã ngân hàng">
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 mb-1">Số tài khoản</label>
                <input type="text" id="account_no" class="w-full border rounded p-3 bg-gray-50 dark:bg-gray-700"
                    placeholder="Nhập số tài khoản">
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-600 dark:text-gray-300 mb-1">Tên chủ tài khoản</label>
                <input type="text" id="account_name" class="w-full border rounded p-3 bg-gray-50 dark:bg-gray-700 uppercase"
                    placeholder="Nhập tên chủ tài khoản">
            </div>
        </div>

        <div class="pt-4">
            <button id="btnSaveBank"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition">
                💾 Lưu thông tin ngân hàng
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token') || '{{ session('api_token') }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const apiBase = '{{ url('chu-tro/profile') }}';

            const bankCodeInput = document.getElementById('bank_code');
            const accountNoInput = document.getElementById('account_no');
            const accountNameInput = document.getElementById('account_name');

            try {
                const res = await fetch(`${apiBase}/bank`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (res.ok && data.data) {
                    bankCodeInput.value = data.data.bank_code ?? '';
                    accountNoInput.value = data.data.account_no ?? '';
                    accountNameInput.value = data.data.account_name ?? '';
                }
            } catch (err) {
                console.warn('Không thể tải thông tin ngân hàng:', err);
            }

            document.getElementById('btnSaveBank').addEventListener('click', async () => {
                const bank_code = bankCodeInput.value.trim();
                const account_no = accountNoInput.value.trim();
                const account_name = accountNameInput.value.trim();

                if (!bank_code || !account_no || !account_name) {
                    Swal.fire('Thiếu thông tin!', 'Vui lòng nhập đầy đủ các trường.', 'warning');
                    return;
                }

                try {
                    const res = await fetch(`${apiBase}/bank`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ bank_code, account_no, account_name })
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        Swal.fire('Thành công', data.message, 'success');
                    } else {
                        Swal.fire('Lỗi', data.message || 'Không thể lưu thông tin.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire('Lỗi kết nối', 'Vui lòng thử lại sau.', 'error');
                }
            });
        });
    </script>


@endsection