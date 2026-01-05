@extends('layouts.app')

@section('title', 'Khu vực Khách Thuê')

@section('content')
    <div class="flex min-h-[80vh]">

        {{-- ================= SIDEBAR ================= --}}
        <aside
            class="w-64 bg-white/90 dark:bg-gray-900/80 backdrop-blur-xl border-r border-gray-200/60 dark:border-gray-700/60 hidden md:flex flex-col justify-between">

            {{-- Header Sidebar --}}
            <div>
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-indigo-600 dark:text-indigo-400 uppercase">
                        Khách Thuê
                    </h2>
                </div>

                {{-- Navigation --}}
                <nav class="mt-4 px-4 flex flex-col space-y-1">
                    <a href="{{ route('khach-thue.dashboard') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-800 {{ Request::is('khach-thue/dashboard') ? 'bg-indigo-100 dark:bg-gray-800 font-semibold' : '' }}">
                        <i class="ri-dashboard-line mr-2 text-indigo-500"></i> Bảng điều khiển
                    </a>

                    <a href="{{ route('khach-thue.phong.index') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-800 {{ Request::is('khach-thue/phong*') ? 'bg-indigo-100 dark:bg-gray-800 font-semibold' : '' }}">
                        <i class="ri-door-line mr-2 text-indigo-500"></i> Phòng trọ
                    </a>

                    <a href="{{ route('khach-thue.yeu-cau-thue.index') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-800 {{ Request::is('khach-thue/yeu-cau-thue*') ? 'bg-indigo-100 dark:bg-gray-800 font-semibold' : '' }}">
                        <i class="ri-mail-send-line mr-2 text-indigo-500"></i> Yêu cầu thuê
                    </a>

                    <a href="{{ route('khach-thue.hop-dong.index') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-800 {{ Request::is('khach-thue/hop-dong*') ? 'bg-indigo-100 dark:bg-gray-800 font-semibold' : '' }}">
                        <i class="ri-file-list-line mr-2 text-indigo-500"></i> Hợp đồng
                    </a>

                    <a href="{{ route('khach-thue.hoa-don.index') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-800 {{ Request::is('khach-thue/hoa-don*') ? 'bg-indigo-100 dark:bg-gray-800 font-semibold' : '' }}">
                        <i class="ri-bill-line mr-2 text-indigo-500"></i> Hóa đơn
                    </a>

                    <a href="{{ route('khach-thue.thong-bao.index') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-800 {{ Request::is('khach-thue/thong-bao*') ? 'bg-indigo-100 dark:bg-gray-800 font-semibold' : '' }}">
                        <i class="ri-notification-2-line mr-2 text-indigo-500"></i> Thông báo
                    </a>

                    {{-- ⭐ Đánh giá --}}
                    <a href="{{ route('khach-thue.danh-gia.index') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-800 {{ Request::is('khach-thue/danh-gia*') ? 'bg-indigo-100 dark:bg-gray-800 font-semibold' : '' }}">
                        <i class="ri-star-line mr-2 text-indigo-500"></i> Đánh giá
                    </a>

                    <a href="{{ route('khach-thue.profile.edit') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-800 {{ Request::is('khach-thue/ho-so*') ? 'bg-indigo-100 dark:bg-gray-800 font-semibold' : '' }}">
                        <i class="ri-user-line mr-2 text-indigo-500"></i> Hồ sơ cá nhân
                    </a>
                </nav>
            </div>

            {{-- Logout --}}
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-gray-800 transition">
                        <i class="ri-logout-box-line mr-2"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        {{-- ================= MAIN CONTENT ================= --}}
        <main class="flex-1 p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                        @yield('page_title', 'Khu vực khách thuê')
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">@yield('page_subtitle')</p>
                </div>

                {{-- Chuông thông báo + dropdown --}}
                @php
                    $miniThongBao = session('thong_bao_moi_nhat', []);
                    $soChuaDoc = session('thong_bao_chua_doc', 0);
                     if (empty($miniThongBao) && $soChuaDoc > 0) {
                        $soChuaDoc = 0;
                    }
                @endphp

                <div class="relative">
                    <button id="tenant-bell-btn"
                            class="relative inline-flex items-center focus:outline-none">
                        <i id="tenant-bell-icon"
                        class="ri-notification-3-line text-2xl text-gray-600 dark:text-gray-200
                                hover:text-indigo-500 transition
                                {{ $soChuaDoc > 0 ? 'tenant-bell-ring' : '' }}"></i>

                        @if($soChuaDoc > 0)
                            <span id="tenant-bell-badge"
                                data-count="{{ $soChuaDoc }}"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs
                                        w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $soChuaDoc > 99 ? '99+' : $soChuaDoc }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown danh sách thông báo nhỏ --}}
                    <div id="tenant-bell-dropdown"
                        class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-900
                                border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl z-50">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                                Thông báo
                            </span>
                            @if($soChuaDoc > 0)
                                <span class="text-xs text-red-500 font-medium">
                                    {{ $soChuaDoc }} chưa đọc
                                </span>
                            @endif
                        </div>

                        <div class="max-h-80 overflow-y-auto">
                            @forelse($miniThongBao as $tb)
                                <a href="{{ $tb['lien_ket'] ?? route('khach-thue.thong-bao.index') }}"
                                class="block px-4 py-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <p class="text-gray-800 dark:text-gray-100 line-clamp-2">
                                        {{ $tb['noi_dung'] ?? '(Không có nội dung)' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($tb['ngay_tao'])->diffForHumans() }}
                                        @if(($tb['da_xem'] ?? 0) == 0)
                                            <span class="inline-block ml-2 text-[10px] px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                                Mới
                                            </span>
                                        @endif
                                    </p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-gray-400">
                                    Không có thông báo nào.
                                </div>
                            @endforelse
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('khach-thue.thong-bao.index') }}"
                            class="block w-full text-center text-xs px-4 py-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50/60 dark:hover:bg-gray-800 rounded-b-xl">
                                Xem tất cả thông báo
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nội dung của từng trang (main pane - AJAX can replace inner HTML) --}}
            <div id="tenant-main-pane" class="bg-white/90 dark:bg-gray-900/70 backdrop-blur-lg rounded-2xl shadow-md p-6">
                @yield('tenant_content')
            </div>
        </main>
    </div>
    
    {{-- Inline script to load certain links into the right-hand pane without full page reload --}}
    <script>
    // 🌟 Toggle dropdown chuông thông báo
    (function () {
        const bellBtn = document.getElementById('tenant-bell-btn');
        const dropdown = document.getElementById('tenant-bell-dropdown');

        if (!bellBtn || !dropdown) return;

        bellBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        // Click outside để đóng dropdown
        document.addEventListener('click', function (e) {
            if (!dropdown.classList.contains('hidden')) {
                if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });

        // Nhấn ESC để đóng
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') dropdown.classList.add('hidden');
        });
    })();
</script>
<style>
    @keyframes tenant-bell-shake {
        0%   { transform: rotate(0); }
        15%  { transform: rotate(15deg); }
        30%  { transform: rotate(-15deg); }
        45%  { transform: rotate(10deg); }
        60%  { transform: rotate(-10deg); }
        75%  { transform: rotate(5deg); }
        100% { transform: rotate(0); }
    }

    .tenant-bell-ring {
        animation: tenant-bell-shake 0.7s ease-in-out infinite;
        transform-origin: top center;
    }
</style>

@endsection