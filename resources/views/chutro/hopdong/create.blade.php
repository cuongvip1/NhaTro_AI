@extends('layouts.chu-tro')

@section('title', 'Thêm hợp đồng thuê')

@section('content')
@php
    // ✅ Nhận giá trị từ URL hoặc từ biến truyền vào
    $selected_phong_id = request()->query('phong_id') ?? ($yeu_cau->phong_id ?? null);
    $selected_khach_thue_id = request()->query('khach_thue_id') ?? ($yeu_cau->khach_thue_id ?? null);
@endphp

<div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-md mt-6">

    {{-- ✅ Flash messages --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    @if(session('ok'))
        <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
            {{ session('ok') }}
        </div>
    @endif

    <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2 mb-8">
        <i class="ri-file-add-line text-3xl"></i>
        Thêm hợp đồng thuê
    </h1>

    <form action="{{ route('chu-tro.hop-dong.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        @if(isset($yeu_cau))
            <input type="hidden" name="yeu_cau_thue_id" value="{{ $yeu_cau->id }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 🔹 Phòng --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phòng</label>
                <select name="phong_id"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Chọn phòng --</option>
                    @foreach ($phongs as $phong)
                        <option value="{{ $phong->id }}"
                            @if(
                                old('phong_id') == $phong->id ||
                                $selected_phong_id == $phong->id ||
                                (isset($yeu_cau) && $yeu_cau->phong_id == $phong->id)
                            ) selected @endif
                            @if($phong->trang_thai !== 'trong') disabled @endif>
                            {{ $phong->so_phong }} — {{ $phong->ten_day_tro ?? '' }}
                            @if($phong->trang_thai !== 'trong')
                                ({{ strtoupper(str_replace('_', ' ', $phong->trang_thai)) }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔹 Khách thuê --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Khách thuê</label>
                <select name="khach_thue_id"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Chọn khách thuê --</option>
                    @foreach ($khach_thues as $kt)
                        <option value="{{ $kt->id }}"
                            @if(
                                old('khach_thue_id') == $kt->id ||
                                $selected_khach_thue_id == $kt->id ||
                                (isset($yeu_cau) && $yeu_cau->khach_thue_id == $kt->id)
                            ) selected @endif>
                            {{ $kt->ho_ten }}
                        </option>
                    @endforeach
                </select>
                @error('khach_thue_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔹 CCCD khách thuê --}}
            <div id="cccdFieldContainer" class="hidden md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    CCCD khách thuê <span class="text-red-500">*</span>
                </label>
                <input type="text" id="cccdInput" name="cccd" placeholder="Nhập hoặc xem CCCD của khách thuê"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p id="cccdStatus" class="text-xs mt-1 text-gray-500 italic"></p>
            </div>

            {{-- 🔹 Ngày bắt đầu & kết thúc --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày bắt đầu</label>
                <input type="date" name="ngay_bat_dau"
                    value="{{ old('ngay_bat_dau', now()->format('Y-m-d')) }}"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày kết thúc</label>
                <input type="date" name="ngay_ket_thuc"
                    value="{{ old('ngay_ket_thuc', now()->addYear()->format('Y-m-d')) }}"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- 🔹 Tiền cọc --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tiền cọc (VNĐ)</label>
                <input type="number" name="tien_coc" min="0" step="1000" placeholder="Nhập số tiền cọc"
                    value="{{ old('tien_coc') }}"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- 🔹 File hợp đồng --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 flex items-center gap-1">
                    File hợp đồng (PDF)
                    <span class="text-gray-400 text-xs">(tùy chọn)</span>
                </label>
                <input type="file" name="file_hop_dong" accept=".pdf"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5
                           file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-600
                           file:text-white hover:file:bg-indigo-700 transition">
            </div>
        </div>

        {{-- 🔹 Người thân --}}
        <div id="nguoiThanContainer" class="mt-10">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                    👨‍👩‍👧‍👦 Người thân / Sống cùng
                </h2>
                <button type="button" id="addNguoiThan"
                    class="bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-full hover:bg-indigo-200 text-sm flex items-center gap-1 transition">
                    <i class="ri-add-line"></i> Thêm người thân
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                <input type="text" name="nguoi_than[0][ho_ten]" class="form-input rounded-lg"
                    placeholder="Họ tên người thân" required>
                <input type="text" name="nguoi_than[0][so_dien_thoai]" class="form-input rounded-lg"
                    placeholder="Số điện thoại">
                <input type="text" name="nguoi_than[0][moi_quan_he]" class="form-input rounded-lg"
                    placeholder="Mối quan hệ (VD: Cha, Mẹ, Bạn...)">
            </div>
        </div>

        {{-- Nút hành động --}}
        <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
            <button type="submit"
                class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white px-5 py-2 rounded-lg flex items-center gap-2 shadow transition">
                <i class="ri-save-3-line"></i> Lưu hợp đồng
            </button>
            <a href="{{ route('chu-tro.hop-dong.index') }}"
                class="px-5 py-2 text-gray-600 hover:text-gray-800 hover:underline transition">Hủy</a>
        </div>
    </form>
</div>

{{-- ✅ JS Section --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const khachThueSelect = document.querySelector('select[name="khach_thue_id"]');
        const submitBtn = document.querySelector('button[type="submit"]');
        const cccdContainer = document.getElementById('cccdFieldContainer');
        const cccdInput = document.getElementById('cccdInput');
        const cccdStatus = document.getElementById('cccdStatus');

        async function loadCCCD(khachId) {
            const token = localStorage.getItem('token');
            if (!token) {
                Swal.fire({ icon: 'error', title: 'Chưa đăng nhập', text: 'Vui lòng đăng nhập lại.' });
                submitBtn.disabled = true;
                return;
            }

            try {
                const response = await fetch(`http://127.0.0.1:8000/api/chu-tro/khach-thue/${khachId}`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const data = await response.json();
                cccdContainer.classList.remove('hidden');

                if (!response.ok) {
                    cccdStatus.textContent = 'Không thể tải thông tin khách thuê.';
                    cccdStatus.className = 'text-sm text-red-500 italic';
                    submitBtn.disabled = true;
                    return;
                }

                if (data.khach_thue?.cccd?.trim()) {
                    cccdInput.value = data.khach_thue.cccd;
                    cccdInput.readOnly = true;
                    cccdInput.classList.add('bg-gray-100');
                    cccdStatus.textContent = 'Khách thuê đã có CCCD.';
                    cccdStatus.className = 'text-xs text-green-600 italic';
                } else {
                    cccdInput.value = '';
                    cccdInput.readOnly = false;
                    cccdInput.classList.remove('bg-gray-100');
                    cccdStatus.textContent = 'Khách thuê chưa có CCCD — vui lòng nhập.';
                    cccdStatus.className = 'text-xs text-yellow-500 italic';
                }
                submitBtn.disabled = false;
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Lỗi kết nối', text: 'Không thể kiểm tra khách thuê.' });
                submitBtn.disabled = true;
            }
        }

        khachThueSelect.addEventListener('change', function () {
            if (this.value) loadCCCD(this.value);
        });

        if (khachThueSelect.value) khachThueSelect.dispatchEvent(new Event('change'));
    });

    // 🔹 Thêm người thân động
    document.getElementById('addNguoiThan').addEventListener('click', function () {
        const container = document.getElementById('nguoiThanContainer');
        const index = container.querySelectorAll('.grid').length;
        const html = `
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                <input type="text" name="nguoi_than[${index}][ho_ten]" class="form-input rounded-lg" placeholder="Họ tên người thân" required>
                <input type="text" name="nguoi_than[${index}][so_dien_thoai]" class="form-input rounded-lg" placeholder="Số điện thoại">
                <input type="text" name="nguoi_than[${index}][moi_quan_he]" class="form-input rounded-lg" placeholder="Mối quan hệ (VD: Anh, Em, Bạn...)">
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
    });
</script>

{{-- ✅ Tự chọn phòng & khách khi mở bằng query --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const phongSelect = document.querySelector('select[name="phong_id"]');
    const khachSelect = document.querySelector('select[name="khach_thue_id"]');
    const phongId = "{{ request()->query('phong_id') }}";
    const khachId = "{{ request()->query('khach_thue_id') }}";

    if (phongId && phongSelect) {
        for (let opt of phongSelect.options) {
            if (opt.value == phongId) opt.selected = true;
        }
    }

    if (khachId && khachSelect) {
        for (let opt of khachSelect.options) {
            if (opt.value == khachId) opt.selected = true;
            khachSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection
