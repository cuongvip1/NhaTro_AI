<!DOCTYPE html>
<html lang="vi" class="scroll-smooth" x-data="darkMode()" x-init="init()" :class="{ 'dark': isDark }">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Site favicon (uses public/images/Logo.png) --}}
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
  <link rel="shortcut icon" href="{{ asset('images/Logo.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">

  <title>@yield('title', 'Hệ Thống Trọ')</title>

  {{-- ✅ Tailwind & AlpineJS --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  {{-- ✅ Remix Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    [x-cloak] {
      display: none !important
    }
  </style>

  <style>
    @keyframes gradientFlow {

      0%,
      100% {
        background-position: 0% 50%;
      }

      50% {
        background-position: 100% 50%;
      }
    }

    .animate-gradientFlow {
      animation: gradientFlow 15s ease infinite;
    }

    .reveal {
      opacity: 0;
      transform: translateY(40px);
      transition: all .8s ease;
    }

    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    @keyframes fade-in {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in {
      animation: fade-in .4s ease-out;
    }
  </style>
</head>

<body class="antialiased flex flex-col min-h-screen transition-all duration-700
             bg-gradient-to-br from-indigo-50 via-white to-blue-50 
             dark:from-gray-950 dark:via-gray-900 dark:to-gray-950
             bg-[length:200%_200%] animate-gradientFlow 
             text-gray-800 dark:text-gray-100">

  {{-- ⚡ FLASH MESSAGE --}}
  @if (session('ok') || session('success') || session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
      class="fixed top-6 right-6 z-[9999] px-5 py-3 rounded-2xl shadow-xl text-white text-sm font-medium 
                                                                                                                                                       animate-fade-in
                                                                                                                                                       {{ session('error') ? 'bg-red-600' : 'bg-gradient-to-r from-green-500 to-emerald-600' }}">
      <i class="ri-information-line mr-2"></i>
      {{ session('ok') ?? session('success') ?? session('error') }}
    </div>
  @endif


  {{-- 🌟 NAVBAR --}}
  @unless (Request::is('login') || Request::is('register'))
    <header x-data="{ open: false, isDark: false }"
      class="sticky top-0 z-50 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 transition-all duration-500"
      :class="{ 'bg-gray-900/80 text-gray-100 shadow-lg': isDark, 'bg-white/80 text-gray-900 shadow-md': !isDark }">
      <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">

        {{-- 🏠 Logo --}}
        <a href="{{ route('home') }}" class="flex items-center space-x-3 font-semibold group">
          <img src="{{ asset('images/Logo.png') }}" alt="Hệ Thống Trọ" class="w-8 h-8 object-contain group-hover:scale-110 transition-transform" onerror="this.onerror=null;this.src='/images/default-avatar.png'">
          <span :class="{ 'text-white': isDark, 'text-gray-800': !isDark }">Hệ Thống Trọ</span>
        </a>

        {{-- 📋 Menu --}}
        <div class="hidden md:flex items-center space-x-6 font-medium">
          <a href="{{ route('home') }}"
            class="hover:text-indigo-500 {{ Request::is('/') ? 'text-indigo-600 font-semibold' : '' }}">Trang chủ</a>
          <a href="{{ route('listing') }}"
            class="hover:text-indigo-500 {{ Request::is('bai-dang') ? 'text-indigo-600 font-semibold' : '' }}">Phòng trọ</a>
          @php
            $u = session('user', []);
          @endphp

          @if (!empty($u) && ($u['vai_tro'] ?? null) === 'khach_thue')
            <a href="{{ route('khach-thue.yeu-thich') }}" class="relative flex items-center space-x-1">
              <i class="ri-heart-line text-pink-500 text-lg"></i>
              <span>Yêu thích</span>

              {{-- 🔢 Badge hiển thị số lượng yêu thích --}}
              <span id="favorite-count"
                class="hidden absolute -top-2 -right-3 bg-pink-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                0
              </span>
            </a>
          @endif

        </div>
        {{-- 👤 User --}}
        <div class="flex items-center space-x-4 relative">
          @if (!session('user'))
            <a href="{{ route('login') }}" class="hover:text-indigo-500">Đăng nhập</a>
            <a href="{{ route('register') }}"
              class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg shadow-md hover:shadow-lg hover:scale-[1.03] transition">
              Đăng ký
            </a>
          @else
            @php
              $u = session('user', []);
              $rawAvatar = $u['anh_dai_dien'] ?? '/images/default-avatar.png';

              // Nếu ảnh chỉ là tên file, thêm prefix URL từ API_BASE_URL
              if (!str_starts_with($rawAvatar, 'http') && !str_starts_with($rawAvatar, '/')) {
                $rawAvatar = rtrim(env('API_BASE_URL', 'http://127.0.0.1:8000'), '/') . '/' . ltrim($rawAvatar, '/');
              }

              // Cache-buster để load lại ảnh mới khi người dùng thay đổi avatar
              $buster = session('avatar_bust') ?? time();
              $avatar = $rawAvatar . (str_contains($rawAvatar, '?') ? '&' : '?') . 'v=' . $buster;

              // Điều hướng theo vai trò
              $role = $u['vai_tro'] ?? 'guest';
              $redirectRoute = match ($role) {
                'chu_tro' => route('chu-tro.dashboard'),
                'khach_thue' => route('khach-thue.dashboard'),
                default => route('home'),
              };
            @endphp

            <a href="{{ $redirectRoute }}" class="flex items-center space-x-3 hover:opacity-80 transition">
              <span class="hidden sm:inline text-gray-700 dark:text-gray-200 font-medium">
                Xin chào, <strong>{{ $u['ho_ten'] ?? 'Người dùng' }}</strong>
              </span>
              <img src="{{ $avatar }}" alt="Avatar"
                class="w-10 h-10 rounded-full border-2 border-indigo-500 object-cover cursor-pointer hover:ring-2 hover:ring-indigo-300 transition"
                onerror="if (!this.dataset.fallback) { this.dataset.fallback = true; this.src='/images/default-avatar.png'; }">
            </a>

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button
                class="flex items-center w-full px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-gray-700 text-sm transition">
                <i class="ri-logout-box-line mr-2"></i> Đăng xuất
              </button>
            </form>
          @endif

          {{-- 🌙 Dark Mode --}}
          <button @click="toggleDark()" class="ml-1 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition">
            <template x-if="!isDark"><i class="ri-moon-line text-xl text-gray-700"></i></template>
            <template x-if="isDark"><i class="ri-sun-line text-xl text-yellow-400"></i></template>
          </button>
        </div>
      </nav>
    </header>
  @endunless

  <main class="flex-grow transition-all duration-500">@yield('content')</main>

  {{-- FOOTER --}}
  <footer class="relative mt-24 text-gray-200 overflow-hidden reveal">
    <div
      class="absolute inset-0 bg-gradient-to-br from-indigo-800 via-purple-800 to-gray-900 dark:from-gray-950 dark:via-indigo-950 dark:to-black bg-[length:200%_200%] animate-gradientFlow">
    </div>
    <div class="relative max-w-7xl mx-auto px-6 py-20 z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
      <div class="lg:col-span-5 reveal-up">
        <div class="flex items-center space-x-3 mb-5">
          <img src="{{ asset('images/Logo.png') }}" alt="Hệ Thống Trọ" class="w-8 h-8 object-contain" onerror="this.onerror=null;this.src='/images/default-avatar.png'">
          <span class="text-2xl font-bold text-white">Hệ Thống Trọ</span>
        </div>
        <p class="text-gray-300/90 text-sm leading-relaxed">Nền tảng giúp kết nối người thuê và chủ trọ hiện đại — nhanh
          chóng, an toàn, tiện lợi chỉ trong vài phút.</p>
      </div>
      <div class="lg:col-span-4 grid grid-cols-2 gap-8 reveal-up anim-delay-150">
        <div>
          <h3 class="text-white font-semibold tracking-wider uppercase mb-5">Liên kết</h3>
          <ul class="space-y-3 text-sm">
            <li><a href="{{ route('home') }}" class="hover:text-indigo-300">Trang chủ</a></li>
            <li><a href="{{ route('listing') }}" class="hover:text-indigo-300">Phòng trọ</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-white font-semibold tracking-wider uppercase mb-5">Liên hệ</h3>
          <ul class="space-y-3 text-sm">
            <li><i class="ri-map-pin-line text-indigo-300 mr-2"></i> TP. Hồ Chí Minh</li>
            <li><i class="ri-mail-line text-indigo-300 mr-2"></i> support@hethongtro.vn</li>
            <li><i class="ri-phone-line text-indigo-300 mr-2"></i> 0123 456 789</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="mt-16 pt-8 border-t border-white/10 text-center text-sm text-gray-400">
      <p>© {{ date('Y') }} <a href="{{ route('home') }}" class="font-semibold text-indigo-300 hover:text-indigo-400">Hệ
          Thống Trọ</a>. Mọi quyền được bảo lưu.</p>
    </div>
  </footer>

  {{-- 🌙 Dark Mode Script --}}
  <script>
    function darkMode() {
      return {
        isDark: false,
        init() {
          const saved = localStorage.getItem('darkMode');
          this.isDark = saved ? JSON.parse(saved) : window.matchMedia('(prefers-color-scheme: dark)').matches;
          this.applyMode();
        },
        toggleDark() {
          this.isDark = !this.isDark;
          localStorage.setItem('darkMode', JSON.stringify(this.isDark));
          this.applyMode();
        },
        applyMode() {
          document.documentElement.classList.toggle('dark', this.isDark);
        }
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      const reveals = document.querySelectorAll(".reveal, .reveal-left, .reveal-right");
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add("active");
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.15 });
      reveals.forEach(el => observer.observe(el));
    });
  </script>

  @stack('scripts')
  @if (session('js_token'))
    <script>
      localStorage.setItem("token", "{{ session('js_token') }}");
    </script>
  @endif


  <script src="//unpkg.com/alpinejs" defer></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      setTimeout(() => Alpine.start(), 50); // delay nhẹ tránh blink
    });
  </script>
  <script>
    window.API_URL = "{{ env('API_URL', 'http://127.0.0.1:8000/api') }}";
    console.log('✅ API_URL =', window.API_URL);
  </script>

  @if (!empty(session('user')) && (session('user')['vai_tro'] ?? null) === 'khach_thue')
    <script>
      const API_BASE_URL = "{{ env('API_BASE_URL', 'http://127.0.0.1:8000') }}/api";

      document.addEventListener('DOMContentLoaded', () => {
        capNhatSoLuongYeuThich();
      });

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
          console.warn('Không thể lấy danh sách yêu thích:', err);
        }
      }

      function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
      }
    </script>
  @endif

</body>

</html>