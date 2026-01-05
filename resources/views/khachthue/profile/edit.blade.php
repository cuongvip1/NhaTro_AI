@extends('layouts.tenant-layout')

@section('title', 'Hồ sơ cá nhân')
@section('page_title', 'Cập nhật hồ sơ')

@section('tenant_content')
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow max-w-2xl mx-auto">
        <form action="{{ route('khach-thue.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Ảnh đại diện hiện tại + chọn mới --}}
            <div class="mb-6 text-center">
                <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200">Ảnh đại diện</label>
                @php
                    $avatar = $user['anh_dai_dien'] ?? 'default-avatar.png';
                    if (!str_starts_with($avatar, 'http')) {
                        $avatar = rtrim(env('API_URL'), '/') . '/' . ltrim($avatar, '/');
                    }
                    $avatar .= '?v=' . time();
                @endphp


                <img id="avatar-preview" src="{{ $avatar }}" alt="Avatar"
                    class="w-24 h-24 rounded-full object-cover mx-auto border mb-3">

                <input type="file" name="anh_dai_dien" accept="image/*" onchange="previewAvatar(event)"
                    class="w-full border rounded-lg p-2 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
            </div>

            {{-- Họ tên --}}
            <div class="mb-4">
                <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Họ tên</label>
                <input type="text" name="ho_ten" value="{{ $user['ho_ten'] ?? '' }}"
                    class="w-full border rounded-lg p-2 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
            </div>

            {{-- Số điện thoại --}}
            <div class="mb-4">
                <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Số điện thoại</label>
                <input type="text" name="so_dien_thoai" value="{{ $user['so_dien_thoai'] ?? '' }}"
                    class="w-full border rounded-lg p-2 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
            </div>

            {{-- Email (readonly) --}}
            <div class="mb-6">
                <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Email</label>
                <input type="email" value="{{ $user['email'] ?? '' }}" readonly
                    class="w-full border rounded-lg p-2 bg-gray-100 dark:bg-gray-700 dark:border-gray-700 dark:text-gray-300 cursor-not-allowed">
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                Lưu thay đổi
            </button>
        </form>


    {{-- Script preview ảnh --}}
    <script>
        function previewAvatar(e) {
            const file = e.target.files[0];
            if (file) {
                const preview = document.getElementById('avatar-preview');
                preview.src = URL.createObjectURL(file);
            }
        }
    </script>
@endsection