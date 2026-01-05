@extends('layouts.tenant-layout')

@section('title', 'Bài đăng yêu thích')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold text-indigo-600 mb-8 text-center">
            💖 Danh sách bài đăng yêu thích
        </h1>

        {{-- 🧩 Trường hợp lỗi hoặc trống --}}
        @if (isset($error))
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-center font-medium">
                {{ $error }}
            </div>
        @elseif (empty($baiDangList) || count($baiDangList) === 0)
            <div class="text-gray-500 text-center py-16">
                <i class="ri-heart-line text-6xl text-pink-400 mb-4"></i>
                <p class="text-lg">Bạn chưa thêm bài đăng nào vào danh sách yêu thích.</p>
                <a href="{{ route('listing') }}"
                    class="inline-block mt-4 px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    → Xem phòng trọ
                </a>
            </div>
        @else
            {{-- 💎 Hiển thị danh sách bài đăng yêu thích --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($baiDangList as $baiDang)
                    <div
                        class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden hover:shadow-lg transition duration-300">
                        {{-- Ảnh đại diện --}}
                        <img src="{{ $baiDang['anh_dai_dien'] ?? asset('images/no-image.png') }}" alt="Ảnh phòng"
                            class="w-full h-48 object-cover">

                        <div class="p-4">
                            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100 truncate">
                                {{ $baiDang['tieu_de'] ?? 'Không có tiêu đề' }}
                            </h2>

                            {{-- 💰 Giá hiển thị --}}
                            <p class="text-indigo-600 font-semibold mt-1">
                                {{ $baiDang['gia_hien_thi'] ?? 'Đang cập nhật' }}
                            </p>

                            {{-- 📍 Địa chỉ --}}
                            <p class="text-gray-500 text-sm mt-1 truncate">
                                {{ $baiDang['dia_chi'] ?? 'Đang cập nhật' }}
                            </p>

                            {{-- ⭐ Rating nếu có --}}
                            @if(!empty($baiDang['rating']))
                                <p class="text-yellow-500 text-sm mt-1">
                                    ⭐ {{ $baiDang['rating'] }} / 5
                                </p>
                            @endif

                            <div class="mt-4 flex justify-between items-center">
                                <a href="{{ route('room.detail', $baiDang['id']) }}"
                                    class="text-indigo-600 font-medium hover:underline">
                                    Xem chi tiết
                                </a>
                                <button class="text-red-500 hover:text-red-700" onclick="xoaYeuThich({{ $baiDang['id'] }})"
                                    title="Bỏ yêu thích">
                                    <i class="ri-heart-fill text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===================== --}}
    {{-- 🔧 Script xử lý xoá yêu thích + badge yêu thích --}}
    {{-- ===================== --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const API_BASE_URL = "{{ env('API_BASE_URL', 'http://127.0.0.1:8000') }}/api";

        document.addEventListener('DOMContentLoaded', () => {
            capNhatSoLuongYeuThich();
        });

        async function xoaYeuThich(id) {
            const token = localStorage.getItem('token') || getCookie('api_token');
            if (!token) {
                Swal.fire({
                    icon: 'info',
                    title: 'Cần đăng nhập',
                    text: 'Vui lòng đăng nhập để sử dụng chức năng này.',
                    confirmButtonText: 'Đăng nhập ngay',
                    confirmButtonColor: '#6366F1'
                }).then(() => {
                    window.location.href = '/login';
                });
                return;
            }

            try {
                const res = await fetch(`${API_BASE_URL}/khach-thue/yeu-thich/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (res.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã xoá khỏi yêu thích',
                        text: data.message || 'Bài đăng đã được gỡ khỏi danh sách.',
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        capNhatSoLuongYeuThich(); // ✅ cập nhật badge sau khi xoá
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Không thể xoá yêu thích. Vui lòng thử lại.',
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kết nối thất bại',
                    text: 'Không thể kết nối đến máy chủ.',
                });
            }
        }

        // ✅ Cập nhật số lượng yêu thích (badge 💖)
        async function capNhatSoLuongYeuThich() {
            const token = localStorage.getItem('token') || getCookie('api_token');
            if (!token) return;

            try {
                const res = await fetch(`${API_BASE_URL}/khach-thue/yeu-thich`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!res.ok) return;

                const data = await res.json();
                const count = data.data?.length ?? 0;

                const badge = document.getElementById('favorite-count');
                if (!badge) return;

                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (err) {
                console.warn('Không thể cập nhật số lượng yêu thích:', err);
            }
        }

        // 🔍 Helper lấy cookie (phòng khi token lưu trong cookie)
        function getCookie(name) {
            const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? match[2] : null;
        }
    </script>
@endsection