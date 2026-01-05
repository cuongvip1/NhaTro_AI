@extends('layouts.chu-tro')

@section('title', 'Thêm Phòng trọ')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-100 py-12">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl p-10 border border-gray-100">

            <a href="{{ route('chu-tro.phong.index') }}"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline mb-8 transition-all duration-200">
                <i class="ri-arrow-left-s-line text-lg mr-1"></i> Quay lại danh sách phòng
            </a>
            <h1 class="text-3xl font-extrabold mb-8 flex items-center gap-3 text-gray-800 tracking-tight">
                <i class="ri-add-circle-line text-indigo-500 text-4xl"></i>
                Thêm phòng mới
            </h1>

            <form action="{{ route('chu-tro.phong.store') }}" method="POST" class="space-y-7">
                @csrf

                {{-- Dãy trọ --}}
                <div>
                    <label for="day_tro_id" class="block text-sm font-medium text-gray-600 mb-2">Dãy trọ</label>
                    <select id="day_tro_id" name="day_tro_id" required
                        class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-gray-50 px-4 py-3 transition-all duration-300">
                        <option value="">-- Chọn dãy trọ --</option>
                        @foreach ($dayTros as $dt)
                            <option value="{{ $dt->id }}" {{ old('day_tro_id') == $dt->id ? 'selected' : '' }}>
                                {{ $dt->ten_day_tro }}
                            </option>
                        @endforeach
                    </select>
                    @error('day_tro_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Số phòng + Tầng (2 cột): Tăng gap-5 thành gap-6 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="so_phong" class="block text-sm font-medium text-gray-600 mb-2">Số phòng</label>
                        <input type="text" id="so_phong" name="so_phong" value="{{ old('so_phong') }}" required
                            placeholder="Ví dụ: P.101"
                            class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-gray-50 px-4 py-3 transition-all duration-300 placeholder-gray-400">
                        @error('so_phong')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tang" class="block text-sm font-medium text-gray-600 mb-2">Tầng</label>
                        <input type="number" id="tang" name="tang" value="{{ old('tang', 1) }}" required min="0"
                            placeholder="Ví dụ: 1"
                            class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-gray-50 px-4 py-3 transition-all duration-300 placeholder-gray-400">
                        @error('tang')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Giá thuê + Sức chứa + Diện tích (3 cột): Tăng gap-5 thành gap-6 --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="gia" class="block text-sm font-medium text-gray-600 mb-2">Giá thuê (VNĐ)</label>
                        <input type="number" id="gia" name="gia" value="{{ old('gia') }}" required min="0"
                            placeholder="3000000"
                            class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-gray-50 px-4 py-3 transition-all duration-300 placeholder-gray-400">
                        @error('gia')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="suc_chua" class="block text-sm font-medium text-gray-600 mb-2">Sức chứa (người)</label>
                        <input type="number" id="suc_chua" name="suc_chua" value="{{ old('suc_chua') }}" required min="1"
                            placeholder="2"
                            class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-gray-50 px-4 py-3 transition-all duration-300 placeholder-gray-400">
                        @error('suc_chua')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dien_tich" class="block text-sm font-medium text-gray-600 mb-2">Diện tích (m²)</label>
                        <input type="number" step="0.1" id="dien_tich" name="dien_tich" value="{{ old('dien_tich') }}"
                            required min="1" placeholder="25.5"
                            class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-gray-50 px-4 py-3 transition-all duration-300 placeholder-gray-400">
                        @error('dien_tich')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div>
                    <label for="trang_thai" class="block text-sm font-medium text-gray-600 mb-2">Trạng thái</label>
                    <select id="trang_thai" name="trang_thai"
                        class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-gray-50 px-4 py-3 transition-all duration-300">
                        <option value="trong" {{ old('trang_thai') == 'trong' ? 'selected' : '' }}>Trống</option>
                        <option value="da_thue" {{ old('trang_thai') == 'da_thue' ? 'selected' : '' }}>Đã thuê</option>
                        <option value="bao_tri" {{ old('trang_thai') == 'bao_tri' ? 'selected' : '' }}>Bảo trì</option>
                    </select>
                </div>

                <div class="flex justify-end gap-4 pt-8 mt-8 border-t border-gray-200">
                    <a href="{{ route('chu-tro.phong.index') }}"
                        class="px-6 py-3 rounded-xl bg-gray-100 text-gray-800 hover:bg-gray-200 transition-all duration-300 font-medium">
                        <i class="ri-close-line mr-1"></i> Hủy
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 flex items-center gap-2 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="ri-save-line text-lg"></i> Lưu phòng
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection