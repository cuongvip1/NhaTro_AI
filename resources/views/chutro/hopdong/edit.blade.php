@extends('layouts.chu-tro')

@section('title', 'Cập nhật hợp đồng thuê')

@section('content')
    <div
        class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-md mt-6 border border-gray-100 dark:border-gray-800">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="ri-edit-box-line text-3xl"></i> Cập nhật hợp đồng thuê
            </h1>
            <a href="{{ route('chu-tro.hop-dong.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <i class="ri-arrow-left-line"></i> Quay lại danh sách
            </a>
        </div>

        {{-- Thông báo --}}
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                ⚠️ {{ session('error') }}
            </div>
        @elseif (session('ok'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                ✅ {{ session('ok') }}
            </div>
        @endif

        <form action="{{ route('chu-tro.hop-dong.update', data_get($hop_dong, 'id')) }}" method="POST"
            enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Phòng --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phòng</label>
                    <select name="phong_id"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                        @foreach ($phongs as $phong)
                            <option value="{{ $phong->id }}" @if ($phong->id == data_get($hop_dong, 'phong_id')) selected @endif>
                                {{ $phong->so_phong }} — {{ $phong->ten_day_tro }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Khách thuê --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Khách thuê</label>
                    <select name="khach_thue_id"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                        @foreach ($khach_thues as $kt)
                            <option value="{{ $kt->id }}" @if ($kt->id == data_get($hop_dong, 'khach_thue_id')) selected @endif>
                                {{ $kt->ho_ten }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- CCCD khách thuê --}}
                <div id="cccdContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        CCCD khách thuê <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="cccdInput" name="cccd" value="{{ data_get($hop_dong, 'cccd', '') }}"
                        placeholder="Nhập hoặc xem CCCD của khách thuê"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p id="cccdStatus" class="text-xs text-gray-500 italic mt-1"></p>
                </div>

                {{-- Ngày bắt đầu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày bắt đầu</label>
                    <input type="date" name="ngay_bat_dau" value="{{ data_get($hop_dong, 'ngay_bat_dau') }}"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                </div>

                {{-- Ngày kết thúc --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày kết thúc</label>
                    <input type="date" name="ngay_ket_thuc" value="{{ data_get($hop_dong, 'ngay_ket_thuc') }}"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                </div>

                {{-- Tiền cọc --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tiền cọc (VNĐ)</label>
                    <input type="number" name="tien_coc" step="1000" min="0"
                        value="{{ data_get($hop_dong, 'tien_coc', 0) }}"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                </div>

                {{-- 📝 Ghi chú --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ghi chú</label>
                    <textarea name="ghi_chu" rows="3" placeholder="Nhập ghi chú hợp đồng (nếu có)"
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">{{ old('ghi_chu', data_get($hop_dong, 'ghi_chu', '')) }}</textarea>
                </div>

                {{-- Trạng thái --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trạng thái</label>
                    <select name="trang_thai" disabled
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5 text-gray-500">
                        <option value="hieu_luc" {{ data_get($hop_dong, 'trang_thai') === 'hieu_luc' ? 'selected' : '' }}>Hiệu
                            lực</option>
                        <option value="het_han" {{ data_get($hop_dong, 'trang_thai') === 'het_han' ? 'selected' : '' }}>Hết
                            hạn</option>
                    </select>
                </div>

                {{-- File hiện tại --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File hợp đồng hiện
                        tại</label>
                    @if (data_get($hop_dong, 'url_file_hop_dong'))
                        <div class="flex items-center gap-2 mt-1">
                            <i class="ri-file-pdf-2-line text-red-500 text-xl"></i>
                            <a href="{{ asset('storage/' . data_get($hop_dong, 'url_file_hop_dong')) }}" target="_blank"
                                class="text-indigo-600 hover:underline">
                                {{ basename(data_get($hop_dong, 'url_file_hop_dong')) }}
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 italic mt-1">Chưa có file hợp đồng nào được tải lên.</p>
                    @endif
                </div>

                {{-- Upload file mới --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tải lên file hợp đồng mới (PDF)
                    </label>
                    <input type="file" name="file_hop_dong" accept="application/pdf" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg p-2.5
                            file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-600
                            file:text-white hover:file:bg-indigo-700 transition">
                </div>
            </div>

            <div class="flex justify-end mt-8 gap-3">
                <button type="submit" onclick="return confirm('Bạn có chắc muốn lưu thay đổi hợp đồng này không?')"
                    class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 hover:from-indigo-700 hover:to-fuchsia-700 text-white px-5 py-2 rounded-lg flex items-center gap-2 shadow transition">
                    <i class="ri-save-3-line"></i> Cập nhật hợp đồng
                </button>
                <a href="{{ route('chu-tro.hop-dong.index') }}"
                    class="px-5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 rounded-lg transition">
                    ❌ Hủy
                </a>
            </div>
        </form>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const khachSelect = document.querySelector('select[name="khach_thue_id"]');
            const cccdContainer = document.getElementById('cccdContainer');
            const cccdInput = document.getElementById('cccdInput');
            const cccdStatus = document.getElementById('cccdStatus');
            const submitBtn = document.querySelector('button[type="submit"]');

            if (cccdInput.value.trim() !== '') {
                cccdContainer.classList.remove('hidden');
                cccdInput.readOnly = true;
                cccdInput.classList.add('bg-gray-100');
                cccdStatus.textContent = 'Khách thuê đã có CCCD.';
                cccdStatus.className = 'text-xs text-green-600 italic';
                submitBtn.disabled = false;
                return;
            }

            if (khachSelect && khachSelect.value) {
                loadCCCD(khachSelect.value);
            }

            khachSelect?.addEventListener('change', function () {
                if (this.value) loadCCCD(this.value);
            });

            async function loadCCCD(khachId) {
                try {
                    const response = await fetch(`/api/chu-tro/khach-thue/${khachId}`, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'include'
                    });


                    cccdContainer.classList.remove('hidden');
                    cccdStatus.textContent = '';
                    cccdStatus.className = '';
                    submitBtn.disabled = false;

                    const data = await response.json();

                    if (!response.ok) {
                        console.warn('API lỗi:', data);
                        if (!cccdInput.value.trim()) {
                            const msg = data?.error ?? 'Không thể lấy thông tin khách thuê.';
                            cccdStatus.textContent = msg;
                            cccdStatus.className = 'text-sm text-red-500 italic';
                            submitBtn.disabled = true;
                        }
                        return;
                    }

                    const khach = data?.khach_thue ?? null;
                    const cccdValue = khach?.cccd ?? '';

                    if (cccdValue.trim() !== '') {
                        cccdInput.value = cccdValue;
                        cccdInput.readOnly = true;
                        cccdInput.classList.add('bg-gray-100');
                        cccdStatus.textContent = 'Khách thuê đã có CCCD.';
                        cccdStatus.className = 'text-xs text-green-600 italic';
                    } else {
                        cccdInput.value = '';
                        cccdInput.readOnly = false;
                        cccdInput.classList.remove('bg-gray-100');
                        cccdStatus.textContent = 'Khách thuê chưa có CCCD — vui lòng nhập để cập nhật.';
                        cccdStatus.className = 'text-xs text-yellow-500 italic';
                    }
                } catch (error) {
                    console.error('Lỗi khi load CCCD:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối!',
                        text: 'Không thể tải thông tin khách thuê.',
                    });
                    if (!cccdInput.value.trim()) {
                        cccdStatus.textContent = 'Không thể tải thông tin khách thuê.';
                        cccdStatus.className = 'text-sm text-red-500 italic';
                        submitBtn.disabled = true;
                    }
                }
            }
        });
    </script>


@endsection