@extends('layouts.tenant-layout')

@section('title', 'Đánh giá phòng')
@section('page_title', 'Đánh giá hợp đồng thuê')

@section('tenant_content')

        {{-- Form gửi đánh giá --}}
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
            ✨ Gửi đánh giá
        </h2>

        <form action="{{ route('khach-thue.danh-gia.store') }}" method="POST" class="mb-8">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Hợp đồng</label>
                    <select name="hop_dong_id" class="w-full border rounded-lg p-2" required>
                        <option value="">-- Chọn hợp đồng --</option>
                        @foreach ($hopDong as $hd)
                            @php
                                $phong = $hd['phong'] ?? [];
                                $dayTro = $phong['day_tro'] ?? [];
                                $daDanhGia = collect($danhGia)->contains('hop_dong_id', $hd['id']);
                            @endphp

                            <option value="{{ $hd['id'] }}" {{ $daDanhGia ? 'disabled' : '' }}>
                                {{ $phong['so_phong'] ?? 'Phòng' }} - {{ $dayTro['ten_day_tro']['ten_day_tro'] ?? '' }}
                                {{ $daDanhGia ? '(Đã đánh giá)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Điểm đánh giá</label>
                    <div id="rating" class="flex flex-row-reverse justify-end gap-1 text-gray-300 text-3xl">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="diem_so" value="{{ $i }}" class="hidden peer star-input"
                                required>
                            <label for="star{{ $i }}" class="cursor-pointer transition transform hover:scale-110">
                                ★
                            </label>
                        @endfor
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm text-gray-600 mb-1">Bình luận</label>
                <textarea name="binh_luan" rows="3" class="w-full border rounded-lg p-2"
                    placeholder="Nhập cảm nhận của bạn về phòng trọ..."></textarea>
            </div>

            <div class="mt-4 text-right">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Gửi đánh giá
                </button>
            </div>
        </form>

        {{-- Danh sách đánh giá --}}
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">💬 Đánh giá đã gửi</h3>
        @forelse ($danhGia as $dg)
            <div class="p-4 mb-3 border border-gray-200 dark:border-gray-700 rounded-xl">
                <p class="text-yellow-500 text-sm">
                    ⭐ {{ str_repeat('★', $dg['diem_so']) }} ({{ $dg['diem_so'] }}/5)
                </p>
                <p class="mt-1 text-gray-700 dark:text-gray-300">
                    {{ $dg['binh_luan'] ?? 'Không có bình luận' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    🏠 {{ $dg['hop_dong']['phong']['so_phong'] ?? '' }}
                    - {{ $dg['hop_dong']['phong']['day_tro']['ten_day_tro'] ?? '' }}
                    • {{ \Carbon\Carbon::parse($dg['ngay_tao'])->format('d/m/Y H:i') }}
                </p>
            </div>
        @empty
            <p class="text-gray-500 text-center mt-4">Bạn chưa gửi đánh giá nào.</p>
        @endforelse

    {{-- ⭐ Hiệu ứng CSS + JS --}}
    <style>
        #rating label {
            color: #d1d5db;
            /* Màu xám mặc định */
        }

        #rating label.active,
        #rating label.hover {
            color: #fbbf24;
            /* Màu vàng sáng */
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const stars = document.querySelectorAll("#rating label");
            const inputs = document.querySelectorAll("#rating input");

            stars.forEach((star, index) => {
                star.addEventListener("mouseenter", () => {
                    stars.forEach((s, i) => {
                        s.classList.toggle("hover", i >= index);
                    });
                });

                star.addEventListener("mouseleave", () => {
                    stars.forEach((s) => s.classList.remove("hover"));
                });

                star.addEventListener("click", () => {
                    inputs.forEach((inp) => inp.checked = false);
                    inputs[5 - index - 1].checked = true;

                    stars.forEach((s, i) => {
                        s.classList.toggle("active", i >= index);
                    });
                });
            });
        });
    </script>
@endsection