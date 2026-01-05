@extends('layouts.chu-tro')

@section('title', 'Chỉnh sửa Dãy trọ')

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-6">
        {{-- 🧭 Header --}}
        <div class="mb-6">
            <h1
                class="text-2xl md:text-3xl font-extrabold tracking-tight flex items-center gap-3 text-gray-800 dark:text-gray-100">
                <span
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white shadow-lg">
                    <i class="ri-edit-2-line text-lg"></i>
                </span>
                Chỉnh sửa Dãy trọ
            </h1>
            <p class="text-sm text-gray-500 mt-1">Cập nhật thông tin chi tiết của dãy trọ.</p>
        </div>

        {{-- ⚙️ Form --}}
        <form action="{{ route('chu-tro.day-tro.update', $dayTro->id) }}" method="POST"
            class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm ring-1 ring-gray-900/5">
            @csrf
            @method('PUT')

            {{-- 🏠 Tên dãy trọ --}}
            <div>
                <label class="block text-sm text-gray-600 mb-1">Tên dãy trọ <span class="text-rose-500">*</span></label>
                <input type="text" name="ten_day_tro" value="{{ old('ten_day_tro', $dayTro->ten_day_tro) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                @error('ten_day_tro')
                    <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 📍 Địa chỉ --}}
            <div>
                <label class="block text-sm text-gray-600 mb-1">Địa chỉ <span class="text-rose-500">*</span></label>
                <input type="text" name="dia_chi" value="{{ old('dia_chi', $dayTro->dia_chi) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                @error('dia_chi')
                    <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔢 Số phòng --}}
            <div>
                <label class="block text-sm text-gray-600 mb-1">Số phòng</label>
                <input type="number" name="so_phong" min="0" value="{{ old('so_phong', $dayTro->so_phong) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                @error('so_phong')
                    <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 📏 Diện tích trung bình --}}
            <div>
                <label class="block text-sm text-gray-600 mb-1">Diện tích trung bình (m²)</label>
                <input type="number" step="0.1" name="dien_tich_tb" min="0"
                    value="{{ old('dien_tich_tb', $dayTro->dien_tich_tb) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                @error('dien_tich_tb')
                    <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 💰 Giá trung bình --}}
            <div>
                <label class="block text-sm text-gray-600 mb-1">Giá trung bình (VNĐ)</label>
                <input type="number" name="gia_trung_binh" min="0"
                    value="{{ old('gia_trung_binh', $dayTro->gia_trung_binh) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                @error('gia_trung_binh')
                    <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🧰 Tiện ích --}}
            <div>
                <label class="block text-sm text-gray-600 mb-1">Tiện ích</label>
                <input type="text" name="tien_ich" value="{{ old('tien_ich', $dayTro->tien_ich) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    placeholder="Wifi, chỗ để xe, máy giặt...">
                @error('tien_ich')
                    <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 📝 Mô tả --}}
            <div>
                <label class="block text-sm text-gray-600 mb-1">Mô tả chi tiết</label>
                <textarea name="mo_ta" rows="5"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('mo_ta', $dayTro->mo_ta) }}</textarea>
                @error('mo_ta')
                    <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔘 Nút hành động --}}
            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('chu-tro.day-tro.index') }}"
                    class="px-5 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Hủy
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow">
                    <i class="ri-save-3-line mr-1"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
@endsection