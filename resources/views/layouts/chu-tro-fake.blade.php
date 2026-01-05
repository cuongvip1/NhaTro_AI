<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Chủ trọ - Hệ Thống Trọ')</title>

    {{-- Tailwind + AlpineJS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    {{-- Preload Chart.js --}}
    <link rel="preload" as="script" href="https://cdn.jsdelivr.net/npm/chart.js">
</head>

<body class="bg-gray-50 text-gray-800 flex min-h-screen">

    {{-- 🧭 SIDEBAR --}}
    <aside
        class="w-64 bg-white/90 backdrop-blur-sm border-r border-gray-200 p-5 flex flex-col sticky top-0 h-screen shadow-lg">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="w-6 h-6 object-contain text-indigo-600" onerror="this.onerror=null;this.src='/images/default-avatar.png'">
                <span class="text-lg font-semibold">Chủ trọ</span>
            </div>

            {{-- 🏠 Nút về trang chủ --}}
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
                <i class="ri-door-line mr-2"></i>Bài đăng

            </a>
            <a href="{{ route('chu-tro.khach.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-user-3-line mr-2"></i>Khách thuê
            </a>
            <a href="{{ route('chu-tro.hop-dong.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-file-list-3-line mr-2"></i>Hợp đồng
            </a>
            <a href="{{ route('chu-tro.nguoi-than.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-bar-chart-line mr-2"></i>Người thân
            </a>
            <a href="{{ route('chu-tro.dien-nuoc.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-pie-chart-line mr-2"></i>Điện nước
            </a>
            <a href="{{ route('chu-tro.hoa-don.index') }}"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50">
                <i class="ri-bill-line mr-2"></i>Hóa đơn
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
                                class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full animate-ping">
                            </span>
                            <span
                                class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full">
                                {{ $thong_bao_chua_xem }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown thông báo --}}
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-3 w-80 bg-white shadow-lg rounded-xl border border-gray-200 z-50">
                        <div class="p-3 border-b font-semibold text-gray-700">
                            Thông báo gần đây
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-gray-100">
                            @forelse($thong_bao ?? [] as $tb)
                                <a href="{{ $tb->lien_ket ?? '#' }}" class="block p-3 text-sm hover:bg-gray-50 transition">
                                    <p class="text-gray-800">{{ $tb->noi_dung }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($tb->ngay_tao)->diffForHumans() }}
                                    </p>
                                </a>
                            @empty
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    Không có thông báo nào
                                </div>
                            @endforelse
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

        @yield('content')
    </main>

    {{-- Hiệu ứng CSS chuông rung --}}
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

    {{-- nơi inject JS --}}
    @stack('scripts')
</body>

</html>