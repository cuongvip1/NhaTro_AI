@extends('layouts.tenant-layout')

@section('title', 'Phòng đang thuê')
@section('page_title', 'Danh sách phòng của bạn')

@section('tenant_content')

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <i class="ri-door-open-line text-indigo-500 text-2xl"></i>
                Phòng bạn đang thuê
            </h2>
        </div>

        @if(empty($phong) || count($phong) === 0)
            <div class="flex flex-col items-center justify-center h-56 text-gray-500 dark:text-gray-400">
                <i class="ri-home-smile-2-line text-4xl mb-3 text-gray-400"></i>
                <p>Bạn hiện chưa có phòng nào đang thuê.</p>
            </div>
        @else
           
                @foreach ($phong as $p)
                    @php
                        // ✅ Lấy bài đăng đang hiển thị (trang_thai = "dang")
                        $baiDangHienTai = collect($p['bai_dang'] ?? [])
                            ->where('trang_thai', 'dang')
                            ->sortByDesc('ngay_cap_nhat')
                            ->first();

                        // ✅ Lấy thumbnail (ảnh đầu tiên của bài đăng đang hiển thị)
                        $thumb = $baiDangHienTai['anh'][0]['url'] ?? '/images/default-room.jpg';

                        // ✅ Lấy tiêu đề bài đăng
                        $tieuDe = $baiDangHienTai['tieu_de'] ?? 'Phòng trọ tiện nghi';
                    @endphp

                    <div
                        class="group bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 relative">

                        {{-- Ảnh thumbnail --}}
                        <div class="relative h-40 overflow-hidden">
                            <img src="{{ $thumb }}" alt="Ảnh phòng {{ $p['so_phong'] ?? '' }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <span
                                class="absolute bottom-2 left-2 bg-white/90 dark:bg-gray-800/90 px-3 py-1 rounded-lg text-xs font-medium shadow">
                                Dãy {{ $p['day_tro']['ten_day_tro'] ?? 'Không xác định' }}
                            </span>
                        </div>

                        {{-- Thông tin phòng --}}
                        <div class="p-5 space-y-1">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                                🏠 {{ $p['so_phong'] ?? 'Phòng trọ' }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2 italic truncate">
                                {{ $tieuDe }}
                            </p>

                            <p>💰 <strong>{{ number_format($p['gia'] ?? 0) }}đ/tháng</strong></p>
                            <p>📏 {{ $p['dien_tich'] ?? '--' }} m² · Tầng {{ $p['tang'] ?? '--' }}</p>
                            <p>👥 {{ $p['suc_chua'] ?? '--' }} người</p>

                            <div class="flex justify-between items-center mt-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full 
                                                            {{ ($p['trang_thai'] ?? '') === 'da_thue'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-gray-200 text-gray-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $p['trang_thai'] ?? 'Không rõ')) }}
                                </span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('khach-thue.phong.show', $p['id']) }}"
                                        class="text-indigo-600 dark:text-indigo-400 text-sm font-semibold hover:underline">
                                        Xem chi tiết →
                                    </a>

                                    @if(!empty($baiDangHienTai['id']))
                                        <button type="button" data-baidang-id="{{ $baiDangHienTai['id'] }}"
                                            class="btn-yeu-thich inline-flex items-center gap-2 text-sm font-medium px-3 py-1.5 rounded-md bg-pink-500 text-white hover:bg-pink-600">
                                            <i class="ri-heart-line"></i>
                                            <span>Quan tâm</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    
                    @push('scripts')
                    <script>
                        async function toggleYeuThichCard(btn) {
                            const baiDangId = btn.dataset.baidangId;
                            const token = localStorage.getItem('token');
                            if (!token) {
                                alert('Vui lòng đăng nhập để sử dụng chức năng này.');
                                window.location.href = '/login';
                                return;
                            }

                            // Simple optimistic toggle
                            const isFav = btn.dataset.fav === 'true';
                            const url = `${window.API_URL}/khach-thue/yeu-thich/${baiDangId}`;
                            const method = isFav ? 'DELETE' : 'POST';

                            try {
                                const res = await fetch(url, {
                                    method,
                                    headers: {
                                        'Authorization': `Bearer ${token}`,
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                });

                                if (!res.ok) throw new Error('Lỗi khi cập nhật yêu thích');

                                // Toggle UI
                                btn.dataset.fav = (!isFav).toString();
                                const icon = btn.querySelector('i');
                                const text = btn.querySelector('span');
                                if (isFav) {
                                    icon.className = 'ri-heart-line';
                                    text.textContent = 'Quan tâm';
                                    btn.classList.remove('bg-red-500');
                                    btn.classList.add('bg-pink-500');
                                } else {
                                    icon.className = 'ri-heart-fill';
                                    text.textContent = 'Đã quan tâm';
                                    btn.classList.remove('bg-pink-500');
                                    btn.classList.add('bg-red-500');
                                }

                            } catch (err) {
                                console.error(err);
                                alert('Không thể cập nhật yêu thích, thử lại sau.');
                            }
                        }

                        document.addEventListener('click', function (e) {
                            const btn = e.target.closest('.btn-yeu-thich');
                            if (!btn) return;
                            toggleYeuThichCard(btn);
                        });
                    </script>
                    @endpush
@endsection