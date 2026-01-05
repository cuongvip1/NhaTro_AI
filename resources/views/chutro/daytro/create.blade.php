@extends('layouts.chu-tro')

@section('title', 'Tạo Dãy trọ mới')

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-6">
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold">Tạo Dãy trọ mới</h1>
            <p class="text-sm text-gray-500 mt-1">Thêm thông tin dãy trọ để quản lý và đăng phòng.</p>
        </div>

        <form action="{{ route('chu-tro.day-tro.store') }}" method="POST" class="bg-white p-6 rounded-2xl shadow-sm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên dãy trọ <span class="text-rose-500">*</span></label>
                    <input type="text" name="ten_day_tro" value="{{ old('ten_day_tro') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-amber-400 focus:border-amber-400">
                    @error('ten_day_tro')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Địa chỉ (khu vực) <span class="text-rose-500">*</span></label>
                    <select name="dia_chi" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 focus:ring-amber-400 focus:border-amber-400">
                        <option value="">-- Chọn khu vực --</option>
                        @foreach($regions ?? [] as $r)
                            <option value="{{ $r->ten_dia_chi }}" {{ old('dia_chi') == $r->ten_dia_chi ? 'selected' : '' }}>{{ $r->ten_dia_chi }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Không thấy khu vực? Bạn có thể thêm trong phần Quản lý khu vực của Admin.</p>
                    @error('dia_chi')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Số phòng</label>
                    <input type="number" name="so_phong" min="0" value="{{ old('so_phong') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('so_phong')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Diện tích TB (m²)</label>
                    <input type="number" step="0.1" name="dien_tich_tb" min="0" value="{{ old('dien_tich_tb') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('dien_tich_tb')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá trung bình (VNĐ)</label>
                    <input type="number" name="gia_trung_binh" min="0" value="{{ old('gia_trung_binh') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('gia_trung_binh')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tiện ích (phân tách bằng dấu phẩy)</label>
                    <input type="text" name="tien_ich" value="{{ old('tien_ich') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Wifi, chỗ để xe, máy giặt...">
                    @error('tien_ich')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                <textarea name="mo_ta" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Mô tả ngắn về dãy trọ, môi trường xung quanh, lưu ý...">{{ old('mo_ta') }}</textarea>
                @error('mo_ta')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('chu-tro.day-tro.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Hủy</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md text-sm font-semibold text-white hover:bg-amber-600">Lưu dãy trọ</button>
            </div>
        </form>
    </div>
@endsection