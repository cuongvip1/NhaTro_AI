<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Chủ trọ - Hệ Thống Trọ')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind + AlpineJS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    {{-- Preload Chart.js --}}
    <link rel="preload" as="script" href="https://cdn.jsdelivr.net/npm/chart.js">
</head>

<body class="bg-gray-50 text-gray-800 flex min-h-screen">

    {{--SIDEBAR --}}
    <aside
        class="w-64 bg-white/90 backdrop-blur-sm border-r border-gray-200 p-5 flex flex-col sticky top-0 h-screen shadow-lg">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="w-6 h-6 object-contain text-indigo-600" onerror="this.onerror=null;this.src='/images/default-avatar.png'">
                <span class="text-lg font-semibold">Chủ trọ</span>
            </div>

            {{--Nút về trang chủ --}}
            <a href="{{ url('/') }}" class="text-xl text-indigo-600 hover:text-indigo-800 transition"
                title="Về trang chủ website">
                <i class="ri-home-3-line"></i>
            </a>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 space-y-1 text-sm font-medium">
            @php $cur = request()->route()?->getName(); @endphp
            <a href="{{ route('chu-tro.dashboard') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50 transition
               {{ str_starts_with($cur, 'chu-tro.dashboard') ? 'bg-gradient-to-r from-indigo-500 to-fuchsia-500 text-white shadow-md' : 'text-gray-700' }}">
                <i class="ri-dashboard-line mr-2"></i> Trang chủ
            </a>
            <a href="{{ route('chu-tro.day-tro.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-community-line mr-2"></i>Dãy trọ
            </a>
            <a href="{{ route('chu-tro.phong.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-door-line mr-2"></i>Phòng
            </a>
            <a href="{{ route('chu-tro.bai-dang.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-file-list-line mr-2"></i>Bài đăng
            </a>
            <a href="{{ route('chu-tro.khachthue.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-user-3-line mr-2"></i>Khách thuê
            </a>
            <a href="{{ route('chu-tro.hop-dong.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-contract-line mr-2"></i>Hợp đồng
            </a>
            <a href="{{ route('chu-tro.nguoi-than.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-group-line mr-2"></i>Người thân
            </a>
            <a href="{{ route('chu-tro.dien-nuoc.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-pie-chart-line mr-2"></i>Điện nước
            </a>
            <a href="{{ route('chu-tro.dich-vu.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-pie-chart-line mr-2"></i>Dịch vụ
            </a>
            <a href="{{ route('chu-tro.hoa-don.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-bill-line mr-2"></i>Hóa đơn
            </a>
            <a href="{{ route('chu-tro.yeu-cau-thue.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-hand-coin-line mr-2"></i>Yêu cầu thuê
            </a>
        </nav>
    </aside>

    {{-- MAIN --}}
    <main class="flex-1 p-8 overflow-y-auto">
        {{-- Header bên phải --}}
        <div class="flex justify-end items-center mb-8">
            <div class="flex items-center space-x-6">

                {{-- Thông báo --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 rounded-full hover:bg-indigo-50 transition">
                        <i class="ri-notification-3-line text-2xl text-gray-700 
                            {{ ($thong_bao_chua_xem ?? 0) > 0 ? 'bell-shake text-indigo-500' : '' }}">
                        </i>
                        @if(($thong_bao_chua_xem ?? 0) > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full animate-ping"></span>
                            <span
                                class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full">
                                {{ $thong_bao_chua_xem }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown thông báo --}}
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-3 w-80 bg-white shadow-lg rounded-xl border border-gray-200 z-50">
                        <div class="p-3 border-b font-semibold text-gray-700">Thông báo gần đây</div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-gray-100">
                            @forelse($thong_bao ?? [] as $tb)
                                <div @click.stop="open = false"
                                    onclick="markAsRead({{ $tb->id }}, '{{ $tb->lien_ket ?? '#' }}')"
                                    class="block p-3 text-sm cursor-pointer transition 
                                                                                                        {{ $tb->da_xem ? 'bg-white opacity-60' : 'bg-indigo-50 font-semibold hover:bg-indigo-100' }}">
                                    <p class="text-gray-800">{{ $tb->noi_dung }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($tb->ngay_tao)->diffForHumans() }}
                                    </p>
                                </div>

                            @empty
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    Không có thông báo nào
                                </div>
                            @endforelse
                        </div>
                        <div class="p-2 text-center border-t">
                            <a href="{{ route('chu-tro.thong-bao.index') }}"
                                class="text-sm text-indigo-600 hover:underline">
                                Xem tất cả thông báo
                            </a>
                        </div>

                    </div>
                </div>

                {{-- Xin chào + Avatar --}}
                <div class="flex items-center space-x-3">
                    <span class="hidden sm:inline text-gray-700 font-medium">
                        Xin chào, <strong>{{ session('user')['ho_ten'] ?? 'Người dùng' }}</strong>
                    </span>
                    <a href="{{ route('chu-tro.dashboard') }}">
                        <img src="{{ session('user')['anh_dai_dien'] ?? '/images/default-avatar.png' }}"
                            class="w-10 h-10 rounded-full border-2 border-indigo-500 object-cover hover:ring-2 hover:ring-indigo-300 transition"
                            alt="Avatar" onerror="this.src='/images/default-avatar.png';">
                    </a>
                </div>
            </div>
        </div>

        {{-- Nội dung chính --}}
        @yield('content')

    </main>

    {{-- CSS hiệu ứng chuông --}}
    <style>
        @keyframes bell-ring {
            0% {
                transform: rotate(0);
            }

            15% {
                transform: rotate(10deg);
            }

            30% {
                transform: rotate(-8deg);
            }

            45% {
                transform: rotate(6deg);
            }

            60% {
                transform: rotate(-4deg);
            }

            75% {
                transform: rotate(2deg);
            }

            100% {
                transform: rotate(0);
            }
        }

        .bell-shake {
            animation: bell-ring 1.2s ease-in-out infinite;
            transform-origin: top center;
            display: inline-block;
            filter: drop-shadow(0 2px 4px rgba(99, 102, 241, 0.4));
        }
    </style>
    {{-- ✅ Popup thông báo toàn hệ thống --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('ok'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '{{ session('ok') }}',
                confirmButtonColor: '#6366f1'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#ef4444'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Thiếu thông tin!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#f59e0b'
            });
        </script>
    @endif
    @stack('scripts')
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script>
        const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
            cluster: "{{ env('PUSHER_APP_CLUSTER') }}"
        });

        const channel = pusher.subscribe('chu-tro-thong-bao');
        channel.bind('ThongBaoMoi', function (data) {
            const bell = document.querySelector('.ri-notification-3-line');
            bell?.classList.add('bell-shake', 'text-indigo-500');

            let badge = document.querySelectorAll('.absolute.-top-1.-right-1.bg-rose-500.text-white.text-xs.w-4.h-4.flex.items-center.justify-center.rounded-full')[1];
            if (badge) {
                let current = parseInt(badge.textContent || "0");
                badge.textContent = current + 1;
            } else {
                const newBadge = document.createElement('span');
                newBadge.className =
                    "absolute -top-1 -right-1 bg-rose-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full";
                newBadge.textContent = "1";
                bell.parentNode.appendChild(newBadge);
            }
            const list = document.querySelector('.max-h-64');
            if (list) {
                const item = document.createElement('a');
                item.href = data.thongBao.lien_ket || '#';
                item.className = 'block p-3 text-sm hover:bg-gray-50 transition';
                item.innerHTML = `
                <p class="text-gray-800">${data.thongBao.noi_dung}</p>
                <p class="text-xs text-gray-500">vừa xong</p>
            `;
                list.prepend(item);
            }
        });
    </script>
    <script>
        function markAsRead(id, link) {
            fetch(`/chu-tro/thong-bao/${id}/mark-as-read`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content,
                    "Accept": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {

                        const badge = document.querySelector(
                            '.absolute.-top-1.-right-1.bg-rose-500.text-white.text-xs.w-4.h-4.flex.items-center.justify-center.rounded-full:last-of-type'
                        );
                        if (badge) {
                            let current = parseInt(badge.textContent || "0");
                            if (current > 1) badge.textContent = current - 1;
                            else badge.remove();
                        }

                        const bell = document.querySelector('.ri-notification-3-line');
                        if (!badge || parseInt(badge.textContent || "0") <= 0) {
                            bell?.classList.remove('bell-shake', 'text-indigo-500');
                        }

                        if (link) window.location.href = link;
                    }
                })
                .catch(() => {
                    if (link) window.location.href = link;
                });
        }
    </script>
    <script>
        async function markAllAsRead() {
            try {
                const res = await fetch('/chu-tro/thong-bao/mark-all-read', {
                    method: 'POST',
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    }
                });

                const data = await res.json();

                if (data.success) {
                    // Xóa hiệu ứng chuông và badge
                    document.querySelectorAll('.ri-notification-3-line').forEach(icon => {
                        icon.classList.remove('bell-shake', 'text-indigo-500');
                    });
                    document.querySelectorAll('.absolute.-top-1.-right-1.bg-rose-500').forEach(el => el.remove());

                    // Làm mờ thông báo trong dropdown
                    document.querySelectorAll('.max-h-64 .bg-indigo-50').forEach(el => {
                        el.classList.remove('bg-indigo-50', 'font-semibold');
                        el.classList.add('bg-white', 'opacity-60');
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Đã đọc tất cả 🔔',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            } catch (err) {
                console.error('markAllAsRead error:', err);
                Swal.fire('Lỗi', 'Không thể đánh dấu tất cả thông báo là đã đọc.', 'error');
            }
        }
    </script>
    <script>
        async function xoaThongBaoDaDoc() {
            const confirm = await Swal.fire({
                icon: 'warning',
                title: 'Xóa tất cả thông báo đã đọc?',
                text: 'Các thông báo chưa đọc sẽ được giữ lại.',
                showCancelButton: true,
                confirmButtonText: 'Xóa luôn',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#ef4444'
            });

            if (!confirm.isConfirmed) return;

            try {
                const res = await fetch('/chu-tro/thong-bao/xoa-da-doc', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã xóa 🎉',
                        text: data.message,
                        timer: 1800,
                        showConfirmButton: false
                    });

                    // ✅ Xóa phần tử HTML của các thông báo đã đọc
                    document.querySelectorAll('.max-h-64 .bg-white.opacity-60').forEach(el => el.remove());
                } else {
                    Swal.fire('Lỗi!', data.message || 'Không thể xóa thông báo.', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Lỗi!', 'Không thể kết nối máy chủ.', 'error');
            }
        }
    </script>

</body>

</html>