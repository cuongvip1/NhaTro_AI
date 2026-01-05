@extends('layouts.chu-tro')
@section('title', 'Thêm khách thuê')

@section('content')
    <div
        class="max-w-4xl mx-auto bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg space-y-8 border border-gray-100 dark:border-gray-800">

        {{-- 🔹 Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="ri-user-add-line text-2xl"></i> Thêm khách thuê
            </h1>
            <a href="{{ route('chu-tro.khachthue.index') }}"
                class="flex items-center gap-1 text-gray-500 hover:text-indigo-600 transition">
                <i class="ri-arrow-left-line"></i> Quay lại
            </a>
        </div>

        {{-- 🔹 Form --}}
        <form method="POST" action="{{ route('chu-tro.khachthue.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Họ tên --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Họ tên</label>
                    <div class="relative">
                        <i class="ri-user-line absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" required
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                                            rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    @error('ho_ten') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Email</label>
                    <div class="relative">
                        <i class="ri-mail-line absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                                            rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    @error('email') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- SĐT --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Số điện thoại</label>
                    <div class="relative">
                        <i class="ri-phone-line absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" name="sdt" value="{{ old('sdt') }}"
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                                            rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    @error('sdt') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- CCCD --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">CCCD</label>
                    <div class="relative">
                        <i class="ri-id-card-line absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" name="cccd" value="{{ old('cccd') }}"
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                                            rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    @error('cccd') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Ghi chú --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Ghi chú</label>
                    <textarea name="ghi_chu" rows="4"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                                        rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ old('ghi_chu') }}</textarea>
                    @error('ghi_chu') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- 🔹 Nút --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white font-medium 
                                    rounded-lg shadow hover:scale-[1.03] transition-all flex items-center gap-1">
                    <i class="ri-save-3-line"></i> Lưu khách thuê
                </button>

                <a href="{{ route('chu-tro.khachthue.index') }}"
                    class="flex items-center gap-1 text-gray-600 hover:text-red-500 font-medium transition">
                    <i class="ri-close-line"></i> Hủy
                </a>
            </div>
        </form>
    </div>
@endsection