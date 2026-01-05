<nav class="bg-white/80 backdrop-blur-sm shadow-sm fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <i class="ri-home-5-line text-indigo-600 text-2xl"></i>
            <span class="font-semibold text-lg text-gray-800">Hệ Thống Nhà Trọ</span>
        </a>

        {{-- Menu --}}
        <div class="flex items-center space-x-6">
            <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">Trang
                chủ</a>
            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">Đăng
                nhập</a>
            <a href="{{ route('register') }}"
                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg shadow hover:shadow-lg transition font-medium">
                Đăng ký
            </a>
        </div>
    </div>
</nav>

{{-- Spacer để không bị che bởi fixed navbar --}}
<div class="h-16"></div>