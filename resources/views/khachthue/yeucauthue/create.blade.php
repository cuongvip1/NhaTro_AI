@extends('layouts.tenant-layout')

@section('title', 'Gửi yêu cầu thuê phòng')
@section('page_title', 'Gửi yêu cầu thuê phòng')

@section('tenant_content')
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
            <i class="ri-home-heart-line text-indigo-500 text-3xl"></i>
            Xác nhận yêu cầu thuê
        </h2>

        {{-- Thông tin phòng --}}
        <div class="bg-indigo-50 dark:bg-gray-900/30 rounded-xl p-5 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">
                🏠 {{ $baiDang['tieu_de'] ?? 'Phòng trọ' }}
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-1">
                📍 {{ $baiDang['dia_chi'] ?? 'Chưa cập nhật địa chỉ' }}
            </p>
            <p class="text-gray-700 dark:text-gray-300">
                💰 <strong>{{ number_format($baiDang['gia_niem_yet'] ?? 0, 0, ',', '.') }}đ / tháng</strong>
            </p>
        </div>

        {{-- Form gửi yêu cầu --}}
        <form action="{{ route('khach-thue.yeu-cau-thue.store') }}" method="POST">
            @csrf
            <input type="hidden" name="bai_dang_id" value="{{ $baiDang['id'] ?? '' }}">

            <div class="mb-5">
                <label for="ghi_chu" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                    ✍️ Ghi chú (tuỳ chọn)
                </label>
                <textarea id="ghi_chu" name="ghi_chu" rows="4"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Ví dụ: Tôi muốn xem phòng vào cuối tuần này..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ url()->previous() }}"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    <i class="ri-arrow-left-line"></i> Quay lại
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                    <i class="ri-send-plane-line"></i> Gửi yêu cầu thuê
                </button>
            </div>
        </form>
    </div>
@endsection