@extends('layouts.chu-tro')
@section('title', 'Cập nhật khách thuê')

@section('content')
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg space-y-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="ri-user-settings-line text-2xl"></i> Cập nhật khách thuê
            </h1>
            <a href="{{ route('chu-tro.khachthue.index') }}" class="text-gray-500 hover:text-indigo-600 transition">
                <i class="ri-arrow-left-line"></i> Quay lại
            </a>
        </div>

        <form method="POST" action="{{ route('chu-tro.khachthue.update', $khach_thue['id']) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Họ tên --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Họ tên</label>
                    <div class="relative">
                        <i class="ri-user-line absolute left-3 top-3.5 text-gray-400 text-lg"></i>
                        <input type="text" name="ho_ten" value="{{ $khach_thue['ho_ten'] ?? '' }}" required
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                                    rounded-lg p-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Email</label>
                    <div class="relative">
                        <i class="ri-mail-line absolute left-3 top-3.5 text-gray-400 text-lg"></i>
                        <input type="email" name="email" value="{{ $khach_thue['email'] ?? '' }}"
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                                    rounded-lg p-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                {{-- SĐT --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Số điện thoại</label>
                    <div class="relative">
                        <i class="ri-phone-line absolute left-3 top-3.5 text-gray-400 text-lg"></i>
                        <input type="text" name="sdt" value="{{ $khach_thue['sdt'] ?? '' }}"
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                                    rounded-lg p-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                {{-- CCCD --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">CCCD</label>
                    <div class="relative">
                        <i class="ri-id-card-line absolute left-3 top-3.5 text-gray-400 text-lg"></i>
                        <input type="text" name="cccd" value="{{ $khach_thue['cccd'] ?? '' }}"
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                                    rounded-lg p-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                {{-- Ghi chú --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Ghi chú</label>
                    <div class="relative">
                        <i class="ri-sticky-note-line absolute left-3 top-3.5 text-gray-400 text-lg"></i>
                        <textarea name="ghi_chu" rows="4"
                            class="w-full pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                                    rounded-lg p-2.5 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ $khach_thue['ghi_chu'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white font-medium 
                            rounded-lg shadow hover:scale-[1.03] transition flex items-center gap-2">
                    <i class="ri-save-3-line text-lg"></i> Cập nhật
                </button>
                <a href="{{ route('chu-tro.khachthue.index') }}"
                    class="text-gray-600 hover:text-red-500 font-medium flex items-center gap-1 transition">
                    <i class="ri-close-line"></i> Hủy
                </a>
            </div>
        </form>
    </div>
@endsection