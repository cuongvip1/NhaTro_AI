@extends('layouts.chu-tro')

@section('title', 'Chỉnh sửa phòng')

@section('content')
    <div class="max-w-3xl mx-auto py-10 px-6">
        {{-- Nút quay lại --}}
        <a href="{{ route('chu-tro.phong.index') }}"
            class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <i class="ri-arrow-left-line mr-1"></i> Quay lại danh sách phòng
        </a>

        {{-- Tiêu đề --}}
        <h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <i class="ri-edit-2-line text-indigo-500 text-3xl"></i>
            Chỉnh sửa phòng {{ $phong->so_phong }}
        </h1>

        {{-- Form chỉnh sửa --}}
        <form action="{{ route('chu-tro.phong.update', $phong->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Dãy trọ --}}
            <div>
                <label for="day_tro_id" class="block text-sm font-medium text-gray-700 mb-1">Dãy trọ</label>
                <select id="day_tro_id" name="day_tro_id" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach ($dayTros as $dt)
                        <option value="{{ $dt->id }}" {{ $phong->day_tro_id == $dt->id ? 'selected' : '' }}>
                            {{ $dt->ten_day_tro }}
                        </option>
                    @endforeach
                </select>
                @error('day_tro_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Số phòng --}}
            <div>
                <label for="so_phong" class="block text-sm font-medium text-gray-700 mb-1">Số phòng</label>
                <input type="text" id="so_phong" name="so_phong" value="{{ old('so_phong', $phong->so_phong) }}" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('so_phong')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Giá thuê --}}
            <div>
                <label for="gia" class="block text-sm font-medium text-gray-700 mb-1">Giá thuê (VNĐ)</label>
                <input type="number" id="gia" name="gia" value="{{ old('gia', $phong->gia) }}" required min="0"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('gia')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sức chứa --}}
            <div>
                <label for="suc_chua" class="block text-sm font-medium text-gray-700 mb-1">Sức chứa (người)</label>
                <input type="number" id="suc_chua" name="suc_chua" value="{{ old('suc_chua', $phong->suc_chua) }}" required
                    min="1"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('suc_chua')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Diện tích --}}
            <div>
                <label for="dien_tich" class="block text-sm font-medium text-gray-700 mb-1">Diện tích (m²)</label>
                <input type="number" step="0.1" id="dien_tich" name="dien_tich"
                    value="{{ old('dien_tich', $phong->dien_tich) }}" required min="1"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('dien_tich')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tầng --}}
            <div>
                <label for="tang" class="block text-sm font-medium text-gray-700 mb-1">Tầng</label>
                <input type="number" id="tang" name="tang" value="{{ old('tang', $phong->tang) }}" required min="0"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('tang')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Trạng thái --}}
            <div>
                <label for="trang_thai" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select id="trang_thai" name="trang_thai"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="trong" {{ $phong->trang_thai == 'trong' ? 'selected' : '' }}>Trống</option>
                    <option value="da_thue" {{ $phong->trang_thai == 'da_thue' ? 'selected' : '' }}>Đã thuê</option>
                    <option value="bao_tri" {{ $phong->trang_thai == 'bao_tri' ? 'selected' : '' }}>Bảo trì</option>
                </select>
            </div>

            {{-- Nút lưu --}}
            <div class="pt-6 flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                    <i class="ri-save-line mr-1"></i> Cập nhật phòng
                </button>
            </div>
        </form>
    </div>
@endsection