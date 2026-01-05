@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-4 relative">

    {{-- Nút trở về trang chủ --}}
    <a href="{{ route('home') }}"
        class="absolute top-6 left-6 flex items-center gap-1 group text-gray-500 hover:text-indigo-600 transition-all z-10">
        <span class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-300 group-hover:border-indigo-500 
                    group-hover:bg-indigo-50 transition-all duration-300">
            <i class="ri-arrow-left-line text-lg"></i>
        </span>
        <span class="ml-1 text-sm font-medium">Trở về trang chủ</span>
    </a>

    {{--  Form đăng nhập --}}
    <div
        x-data="{ show: false }"
        x-init="setTimeout(() => show = true, 100)"
        class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-[0_8px_40px_rgba(0,0,0,0.08)] overflow-hidden w-full max-w-5xl grid grid-cols-1 md:grid-cols-2"
    >

        {{-- Left: Form --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 -translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="p-10 flex flex-col justify-center"
        >

            {{-- Header --}}
            <div class="mb-8 text-center md:text-left">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight mb-2">Chào mừng trở lại 👋</h1>
                <p class="text-gray-500 text-sm">Đăng nhập để tiếp tục với hệ thống của bạn</p>
            </div>

            {{-- Alerts --}}
            @if (session('ok'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg p-3">{{ session('ok') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg p-3">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg p-3">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 hover:border-indigo-400 text-gray-900 placeholder-gray-400 transition-all"
                        placeholder="example@gmail.com"
                    />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mật khẩu --}}
                <div>
                    <label for="mat_khau" class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu</label>
                    <input
                        type="password"
                        id="mat_khau"
                        name="mat_khau"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 hover:border-indigo-400 text-gray-900 placeholder-gray-400 transition-all"
                        placeholder="Nhập mật khẩu"
                    />
                    @error('mat_khau')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember & Forgot --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition"
                            {{ old('remember') ? 'checked' : '' }}
                        />
                        <span class="ml-2 text-gray-600 select-none">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium transition">Quên mật khẩu?</a>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-semibold py-3 rounded-xl shadow-md hover:shadow-lg focus:ring-4 focus:ring-indigo-200 transition-transform transform hover:scale-[1.02]"
                >
                    <i class="ri-login-box-line mr-2"></i> Đăng nhập
                </button>

                {{-- Divider --}}
                <div class="flex items-center my-4">
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <span class="px-3 text-gray-400 text-sm">Hoặc</span>
                    <div class="flex-1 h-px bg-gray-300"></div>
                </div>

                {{-- login --}}
               <div class="w-full">
                    <a
                        href="{{ env('API_BASE_URL') }}/api/auth/google/redirect"
                        class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 transition"
                    >
                        <i class="ri-google-fill mr-2 text-red-500"></i>
                        <span class="text-sm font-medium text-gray-700">Google</span>
                    </a>
                </div>

                {{-- Register --}}
                <p class="text-sm text-gray-600 text-center mt-6">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Đăng ký ngay</a>
                </p>
            </form>
        </div>

        {{-- Right: Image --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="hidden md:block relative"
        >
            <img
                src="{{ asset('images/tro.jpg') }}"
                alt="Phòng trọ đẹp"
                class="object-cover w-full h-full scale-105 hover:scale-110 transition-transform duration-700 ease-out"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/25 via-transparent to-transparent"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs"></script>
@endpush