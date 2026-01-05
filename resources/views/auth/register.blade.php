@extends('layouts.app')

@section('title', 'Đăng ký tài khoản')

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-4 relative">

        {{-- Nút trở về trang chủ --}}
        <a href="{{ route('home') }}"
            class="absolute top-6 left-6 flex items-center gap-1 group text-gray-500 hover:text-indigo-600 transition-all z-10">
            <span
                class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-300 group-hover:border-indigo-500 
                                                                                                 group-hover:bg-indigo-50 transition-all duration-300">
                <i class="ri-arrow-left-line text-lg"></i>
            </span>
            <span class="ml-1 text-sm font-medium">Trở về trang chủ</span>
        </a>

        {{-- Form đăng ký --}}
        <div x-data="{ show: false, selectedRole: '{{ old('role') ?? 'khach_thue' }}' }"
            x-init="setTimeout(() => show = true, 150)"
            class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-[0_8px_40px_rgba(0,0,0,0.08)] overflow-hidden w-full max-w-5xl grid grid-cols-1 md:grid-cols-2">


            {{-- Left: Form --}}
            <div x-show="show" x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 -translate-x-6" x-transition:enter-end="opacity-100 translate-x-0"
                class="p-10 flex flex-col justify-center relative">

                {{-- Header --}}
                <div class="mb-8 text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-800 tracking-tight mb-2">Tạo tài khoản mới ✨</h1>
                    <p class="text-gray-500 text-sm">Đăng ký để bắt đầu sử dụng hệ thống</p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('register.post') }}" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @csrf

                    {{-- Họ và tên --}}
                    <div>
                        <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 hover:border-indigo-400 
                                                                                                           text-gray-900 placeholder-gray-400 transition-all" placeholder="Nguyễn Văn A" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 hover:border-indigo-400 
                                                                                                           text-gray-900 placeholder-gray-400 transition-all" placeholder="example@gmail.com" />
                    </div>

                    {{-- Số điện thoại --}}
                    <div>
                        <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">Số điện
                            thoại</label>
                        <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 hover:border-indigo-400 
                                                                                                           text-gray-900 placeholder-gray-400 transition-all"
                            placeholder="0123 456 789" />
                    </div>
                    {{-- Mật khẩu --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 hover:border-indigo-400 
                                                                                                           text-gray-900 placeholder-gray-400 transition-all"
                            placeholder="Nhập mật khẩu" />
                    </div>

                    {{-- Xác nhận mật khẩu --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Xác nhận mật khẩu
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 hover:border-indigo-400
                                            text-gray-900 placeholder-gray-400 transition-all"
                            placeholder="Nhập lại mật khẩu" />
                    </div>


                    {{-- Vai trò --}}
                    <div class="col-span-1 md:col-span-2 mt-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Vai trò</label>
                        <div class="flex flex-col sm:flex-row gap-4">

                            {{-- Khách thuê --}}
                            <div @click="selectedRole = (selectedRole === 'khach_thue' ? '' : 'khach_thue')"
                                class="flex-1 cursor-pointer relative rounded-xl border transition-all duration-300 overflow-hidden"
                                :class="selectedRole === 'khach_thue'
                                                                                                                ? 'border-indigo-500 bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-lg'
                                                                                                                : 'border-gray-300 hover:border-indigo-400 text-gray-700'">
                                <div class="px-4 py-3 text-center font-medium relative">
                                    <i class="ri-home-5-line mr-1 text-lg"></i> Khách thuê
                                    <span x-show="selectedRole === 'khach_thue'"
                                        class="absolute top-3 right-3 bg-white/90 text-indigo-600 rounded-full p-1 shadow-sm transition-all">
                                        <i class="ri-check-line text-sm font-bold"></i>
                                    </span>
                                </div>
                            </div>

                            {{-- Chủ trọ --}}
                            <div @click="selectedRole = (selectedRole === 'chu_tro' ? '' : 'chu_tro')"
                                class="flex-1 cursor-pointer relative rounded-xl border transition-all duration-300 overflow-hidden"
                                :class="selectedRole === 'chu_tro'
                                                                                                                ? 'border-indigo-500 bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-lg'
                                                                                                                : 'border-gray-300 hover:border-indigo-400 text-gray-700'">
                                <div class="px-4 py-3 text-center font-medium relative">
                                    <i class="ri-building-line mr-1 text-lg"></i> Chủ trọ
                                    <span x-show="selectedRole === 'chu_tro'"
                                        class="absolute top-3 right-3 bg-white/90 text-indigo-600 rounded-full p-1 shadow-sm transition-all">
                                        <i class="ri-check-line text-sm font-bold"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="role" :value="selectedRole" />
                    </div>

                    {{-- Submit --}}
                    <div class="col-span-1 md:col-span-2 mt-4">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-semibold py-3 rounded-xl shadow-md hover:shadow-lg focus:ring-4 focus:ring-indigo-200 transition-transform transform hover:scale-[1.02]">
                            <i class="ri-user-add-line mr-2"></i> Tạo tài khoản
                        </button>
                    </div>
                </form>

                {{-- Footer --}}
                <p class="text-sm text-gray-600 text-center mt-6">
                    Đã có tài khoản?
                    <a href="{{ route('login') }}"
                        class="text-indigo-600 hover:text-indigo-700 font-semibold transition">Đăng nhập ngay</a>
                </p>

                <p class="text-xs text-gray-500 text-center mt-4">
                    Bằng việc đăng ký, bạn đồng ý với
                    <a href="#" class="text-indigo-600 hover:text-indigo-700">Điều khoản dịch vụ</a> và
                    <a href="#" class="text-indigo-600 hover:text-indigo-700">Chính sách bảo mật</a>.
                </p>
            </div>

            {{-- Right: Image --}}
            <div x-show="show" x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                class="hidden md:block relative">
                <img src="{{ asset('images/tro2.jpg') }}" alt="Phòng trọ"
                    class="object-cover w-full h-full scale-105 hover:scale-110 transition-transform duration-700 ease-out" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/25 via-transparent to-transparent"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script defer src="https://unpkg.com/alpinejs"></script>
@endpush