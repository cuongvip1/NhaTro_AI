<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
  {{-- Favicon for admin area --}}
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
  <link rel="shortcut icon" href="{{ asset('images/Logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .scrollbar::-webkit-scrollbar{width:8px;height:8px}
        .scrollbar::-webkit-scrollbar-thumb{background:#c7c7c7;border-radius:9999px}
        .scrollbar::-webkit-scrollbar-track{background:transparent}
    </style>
    @stack('head')
  </head>
  <body class="bg-gray-50 text-gray-800 min-h-screen">
    <script>
      // Early-capture navigation handler: listen on pointerdown so we run before most click interceptors.
      (function(){
        function goToHref(el) {
          try {
            var a = el.closest && el.closest('.force-nav');
            if (!a) return false;
            var href = a.getAttribute('href');
            if (!href || href === '#') return false;
            // stop other handlers and navigate immediately
            window.location.href = href;
            return true;
          } catch(e) { console.debug('earlyNav error', e); return false; }
        }

        document.addEventListener('pointerdown', function(e){
          try {
            var el = e.target;
            if (goToHref(el)) {
              e.preventDefault();
              e.stopImmediatePropagation();
            }
          } catch(err) { console.debug('pointerdown handler error', err); }
        }, true);
      })();
    </script>
    <div class="flex min-h-screen">
  <!-- Sidebar (sticky on desktop) -->
  <aside class="hidden md:flex md:w-64 flex-col bg-gradient-to-b from-indigo-700 to-blue-700 text-white sticky top-0 h-screen">
        <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <img src="{{ asset('images/Logo.png') }}" alt="Admin" class="w-8 h-8 object-contain">
            <span class="font-semibold">Admin Panel</span>
          </div>
        </div>
  <!-- Remove internal overflow so the whole page scrolls together with the sidebar -->
  <nav class="flex-1 overflow-y-auto p-3 space-y-1 scrollbar">
          <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10' : '' }}">
            <i class="ri-dashboard-line mr-3 text-xl"></i> Trang chủ
          </a>
          <a href="{{ route('admin.accounts') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.accounts') ? 'bg-white/10' : '' }}">
            <i class="ri-user-3-line mr-3 text-xl"></i> Quản lý Tài khoản
          </a>
          <a href="{{ route('admin.posts') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.posts') ? 'bg-white/10' : '' }}">
            <i class="ri-article-line mr-3 text-xl"></i> Quản lý Bài viết
          </a>
          <a href="{{ route('admin.regions') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.regions') ? 'bg-white/10' : '' }}">
            <i class="ri-map-pin-line mr-3 text-xl"></i> Quản lý Khu vực
          </a>
          <a href="{{ route('admin.approvals') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.approvals') ? 'bg-white/10' : '' }}">
            <i class="ri-checkbox-circle-line mr-3 text-xl"></i> Xét duyệt Bài viết
          </a>
          <a href="{{ route('admin.permissions') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.permissions') ? 'bg-white/10' : '' }}">
            <i class="ri-shield-keyhole-line mr-3 text-xl"></i> Quản lý Phân quyền
          </a>
        </nav>
        <div class="px-4 py-4 border-t border-white/10 text-sm opacity-90">© {{ date('Y') }} Admin</div>
      </aside>

      <!-- Main -->
      <div class="flex-1 flex flex-col">
        <!-- Top bar -->
        <header class="sticky top-0 bg-white shadow-sm z-10">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <button class="md:hidden p-2 rounded hover:bg-gray-100" onclick="document.getElementById('mobileMenu').classList.remove('hidden')">
                <i class="ri-menu-line text-xl"></i>
              </button>
              {{-- Search input removed per request --}}
            </div>
            <div class="flex items-center gap-3">
              <a href="{{ route('admin.accounts') }}" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                Quản lý tài khoản
              </a>

              <!-- Avatar with dropdown -->
              <div class="relative">
                <button id="avatarBtn" aria-haspopup="true" aria-expanded="false" class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-blue-500 grid place-items-center text-white focus:outline-none">
                  <i class="ri-user-3-line"></i>
                </button>

                <div id="avatarMenu" class="hidden absolute right-0 mt-2 w-40 bg-white rounded shadow-md ring-1 ring-black ring-opacity-5 py-1 text-sm">
                  <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-600 hover:bg-gray-50 px-2 py-2 rounded">Đăng xuất</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
          @yield('content')
        </main>
      </div>
    </div>

    <!-- Mobile drawer -->
    <div id="mobileMenu" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-black/40" onclick="this.parentElement.classList.add('hidden')"></div>
      <div class="absolute inset-y-0 left-0 w-72 bg-indigo-700 text-white p-4">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-2">
              <img src="{{ asset('images/Logo.png') }}" alt="Admin" class="w-8 h-8 object-contain">
              <span class="font-semibold">Admin Panel</span>
            </div>
          <button class="p-2" onclick="document.getElementById('mobileMenu').classList.add('hidden')"><i class="ri-close-line text-2xl"></i></button>
        </div>
        <nav class="space-y-1">
          <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-white/10">Trang chủ</a>
          <a href="{{ route('admin.accounts') }}" class="block px-3 py-2 rounded hover:bg-white/10">Quản lý Tài khoản</a>
          <a href="{{ route('admin.posts') }}" class="block px-3 py-2 rounded hover:bg-white/10">Quản lý Bài viết</a>
          <a href="{{ route('admin.regions') }}" class="block px-3 py-2 rounded hover:bg-white/10">Quản lý Khu vực</a>
          <a href="{{ route('admin.approvals') }}" class="block px-3 py-2 rounded hover:bg-white/10">Xét duyệt Bài viết</a>
          <a href="{{ route('admin.permissions') }}" class="block px-3 py-2 rounded hover:bg-white/10">Quản lý Phân quyền</a>
        </nav>
      </div>
    </div>

  <script>
    // Global bypass: for anchors/buttons marked with data-no-ajax
    (function(){
      function shouldBypass(el) {
        if (!el) return false;
        if (el.closest && el.closest('[data-no-ajax]')) return true;
        if (el.closest && el.closest('#rejectModalContent')) return true;
        if (el.closest && el.closest('#editRoleModalContent')) return true;
        return false;
      }

      document.addEventListener('click', function(e){
        try {
          var el = e.target;
          if (shouldBypass(el)) {
            e.stopImmediatePropagation();
            e.stopPropagation();
            if (window.console && console.debug) console.debug('BYPASS click for', el);
          }
        } catch(err) {
          console.debug('bypass error', err);
        }
      }, true);
    })();
  </script>

  <script>
    // Avatar dropdown toggle
    (function(){
      var btn = document.getElementById('avatarBtn');
      var menu = document.getElementById('avatarMenu');
      if (!btn || !menu) return;

      function hide() {
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
      }
      function show() {
        menu.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
      }

      btn.addEventListener('click', function(e){
        e.stopPropagation();
        if (menu.classList.contains('hidden')) show(); else hide();
      });

      // close when clicking outside
      document.addEventListener('click', function(e){
        if (!menu.contains(e.target) && !btn.contains(e.target)) hide();
      });

      // close on escape
      document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hide(); });
    })();
  </script>

  @stack('scripts')

  </body>
  </html>
