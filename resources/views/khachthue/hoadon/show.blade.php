@extends('layouts.tenant-layout')

@section('title', 'Chi tiết hóa đơn')
@section('page_title', 'Chi tiết hóa đơn')

@section('tenant_content')
@php
    $hd = $chiTiet ?? [];
@endphp

<div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden">

    {{-- 🧾 HEADER --}}
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            🧾 Hóa đơn tháng {{ $hd['thang'] ?? '--' }}
        </h2>
        <span class="px-3 py-1 text-sm rounded-full font-medium
            @switch($hd['trang_thai'] ?? '')
                @case('da_thanh_toan') bg-green-100 text-green-700 @break
                @case('chua_thanh_toan') bg-red-100 text-red-700 @break
                @case('mot_phan') bg-yellow-100 text-yellow-700 @break
                @case('cho_xac_nhan') bg-amber-100 text-amber-700 @break
                @default bg-gray-200 text-gray-600
            @endswitch">
            {{ ucfirst(str_replace('_', ' ', $hd['trang_thai'] ?? 'Không rõ')) }}
        </span>
    </div>

    {{-- 🏠 THÔNG TIN CƠ BẢN --}}
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 dark:text-gray-300">
        <div>
            <p><strong>🏠 Phòng:</strong> {{ $hd['phong']['so_phong'] ?? 'N/A' }}</p>
            <p><strong>📍 Dãy trọ:</strong> {{ $hd['day_tro']['ten_day_tro'] ?? 'N/A' }}</p>
            <p><strong>🏡 Tiền phòng:</strong> {{ number_format($hd['tien_phong'] ?? 0) }}đ</p>
            <p><strong>💰 Tổng tiền:</strong> 
                <span class="text-red-600 font-semibold">
                    {{ number_format($hd['tong_tien'] ?? 0) }}đ
                </span>
            </p>
            @php
    $han = $hd['han_thanh_toan'] ?? null;
    try {
        $ngay = $han ? \Carbon\Carbon::parse($han)->format('d/m/Y') : null;
    } catch (\Exception $e) {
        $ngay = '--';
    }
@endphp
           

<p><strong>📅 Hạn thanh toán:</strong> {{ $ngay ?? '--' }}</p>

        </div>

        <div>
            <p><strong>👤 Chủ trọ:</strong> {{ $hd['chu_tro']['ho_ten'] ?? 'Chưa cập nhật' }}</p>
            <p><strong>📞 SĐT:</strong> {{ $hd['chu_tro']['so_dien_thoai'] ?? '--' }}</p>
            <p><strong>🏠 Địa chỉ:</strong> {{ $hd['day_tro']['dia_chi'] ?? 'Không có dữ liệu' }}</p>
        </div>
    </div>

    {{-- CHI TIẾT DỊCH VỤ --}}
    @if(!empty($hd['chi_tiet_dich_vu']))
    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-indigo-600 dark:text-indigo-400 mb-3">💡 Chi tiết dịch vụ</h3>
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="p-2 text-left">Dịch vụ</th>
                    <th class="p-2 text-center">Số lượng</th>
                    <th class="p-2 text-center">Đơn giá</th>
                    <th class="p-2 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hd['chi_tiet_dich_vu'] as $dv)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                        <td class="p-2">{{ $dv['ten_dich_vu'] ?? '--' }}</td>
                        <td class="p-2 text-center">{{ $dv['so_luong'] ?? '--' }}</td>
                        <td class="p-2 text-center">{{ number_format($dv['don_gia'] ?? 0) }}đ</td>
                        <td class="p-2 text-right font-medium">{{ number_format($dv['thanh_tien'] ?? 0) }}đ</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-semibold bg-gray-50 dark:bg-gray-900/40">
                    <td colspan="3" class="p-2 text-right">Tổng dịch vụ:</td>
                    <td class="p-2 text-right">{{ number_format(collect($hd['chi_tiet_dich_vu'])->sum('thanh_tien')) }}đ</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- CHI TIẾT ĐIỆN NƯỚC --}}
    @if (!empty($hd['chi_tiet_dien_nuoc']))
    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-amber-600 dark:text-amber-400 mb-3">⚡ Điện & Nước</h3>
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="p-2 text-left">Loại</th>
                    <th class="p-2 text-center">Chỉ số cũ</th>
                    <th class="p-2 text-center">Chỉ số mới</th>
                    <th class="p-2 text-center">Tiêu thụ</th>
                    <th class="p-2 text-center">Đơn giá</th>
                    <th class="p-2 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hd['chi_tiet_dien_nuoc'] as $ct)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                        <td class="p-2">{{ $ct['ten_dich_vu'] ?? '---' }}</td>
                        <td class="p-2 text-center">{{ $ct['chi_so_cu'] ?? '--' }}</td>
                        <td class="p-2 text-center">{{ $ct['chi_so_moi'] ?? '--' }}</td>
                        <td class="p-2 text-center">{{ $ct['san_luong'] ?? '--' }}</td>
                        <td class="p-2 text-center">{{ number_format($ct['don_gia'] ?? 0) }}đ</td>
                        <td class="p-2 text-right">{{ number_format($ct['thanh_tien'] ?? 0) }}đ</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-semibold bg-gray-50 dark:bg-gray-900/40">
                    <td colspan="5" class="p-2 text-right">Tổng điện nước:</td>
                    <td class="p-2 text-right">
                        {{ number_format(collect($hd['chi_tiet_dien_nuoc'])->sum('thanh_tien')) }}đ
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- TỔNG KẾT --}}
    <div class="px-6 py-4 flex justify-between items-center border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
        <div class="text-right">
            <p class="text-gray-600 dark:text-gray-300">Tổng cộng:</p>
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 hover:scale-105 transition-transform">
                {{ number_format($hd['tong_tien'] ?? 0) }}đ
            </p>
        </div>

        {{-- Nút thanh toán QR --}}
        @if(in_array($hd['trang_thai'] ?? '', ['chua_thanh_toan','mot_phan']))
            <button id="btnThanhToanQR"
                class="inline-flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition">
                <i class="ri-qr-code-line"></i> Thanh toán ngay
            </button>
        @endif
    </div>

    {{-- NÚT QUAY LẠI --}}
    <div class="p-6 flex justify-end">
        <a href="{{ route('khach-thue.hoa-don.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">
            <i class="ri-arrow-left-line"></i> Quay lại danh sách
        </a>
    </div>
</div>

{{-- Modal QR --}}
<div id="qrModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl w-full max-w-md text-center relative">
        <button id="closeQR" class="absolute top-2 right-3 text-gray-400 hover:text-gray-700">
            <i class="ri-close-line text-2xl"></i>
        </button>

        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">
            Quét mã QR để thanh toán
        </h3>

        @php
            $bank = [
                'bank_code' => $hd['chu_tro']['bank_code'] ?? 'MB',
                'account_no' => $hd['chu_tro']['account_no'] ?? '0000000000',
                'account_name' => strtoupper($hd['chu_tro']['account_name'] ?? $hd['chu_tro']['ho_ten'] ?? 'TEN CHU TRO'),
                'amount' => $hd['tong_tien'] ?? 0,
                'description' => "Thanh toan hoa don thang {$hd['thang']}"
            ];

            $qrUrl = "https://img.vietqr.io/image/{$bank['bank_code']}-{$bank['account_no']}-compact2.png?amount={$bank['amount']}&addInfo=" . urlencode($bank['description']) . "&accountName=" . urlencode($bank['account_name']);
        @endphp

        {{-- Ảnh QR --}}
        <img id="qrImage" src="{{ $qrUrl }}" alt="QR Thanh toán"
            class="mx-auto w-60 h-60 rounded-xl shadow-lg bg-white p-2 border border-gray-200">

        {{-- Mô tả --}}
        <p class="text-sm text-gray-500 mt-3">
            Quét bằng app ngân hàng để thanh toán chính xác số tiền.
        </p>

        {{-- Nút hành động --}}
        <div class="mt-5 flex flex-col sm:flex-row justify-center gap-3">
            <button id="downloadQR"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                <i class="ri-download-2-line"></i> Tải mã QR
            </button>

            <button id="btnDaChuyen"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition">
                <i class="ri-check-line"></i> Tôi đã chuyển khoản xong
            </button>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnQR = document.getElementById('btnThanhToanQR');
    const modal = document.getElementById('qrModal');
    const closeBtn = document.getElementById('closeQR');
    const confirmBtn = document.getElementById('btnDaChuyen'); // ✅ sửa lại đúng ID
    const downloadBtn = document.getElementById('downloadQR');
    const qrImage = document.getElementById('qrImage');

    const apiBase = '{{ rtrim(env('API_URL', 'http://127.0.0.1:8000/api'), '/') }}';
    const token = localStorage.getItem('token') || '{{ session('api_token') }}';
    const hoaDonId = {{ $hd['id'] ?? 0 }};

    // 🧾 Mở modal QR
    if (btnQR) {
        btnQR.onclick = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };
    }

    // ❌ Đóng modal
    if (closeBtn) closeBtn.onclick = () => modal.classList.add('hidden');
    if (modal) modal.onclick = e => { if (e.target === modal) modal.classList.add('hidden'); };

    // 💾 Tải ảnh QR xuống
    if (downloadBtn && qrImage) {
        downloadBtn.onclick = async () => {
            try {
                const response = await fetch(qrImage.src, { mode: 'cors' });
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `ma_qr_hoadon_${hoaDonId}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            } catch (err) {
                console.error('❌ Lỗi tải mã QR:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Không thể tải mã QR!',
                    text: 'Vui lòng thử lại hoặc bấm chuột phải để lưu ảnh thủ công.'
                });
            }
        };
    }

    // ✅ Khi khách bấm “Tôi đã chuyển khoản xong”
    if (confirmBtn) {
        confirmBtn.addEventListener('click', async () => {
            if (!token) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Vui lòng đăng nhập!',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            const confirmResult = await Swal.fire({
                title: 'Bạn đã chuyển khoản xong chưa?',
                text: 'Chỉ bấm xác nhận sau khi bạn đã chuyển khoản thành công cho chủ trọ.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Tôi đã chuyển xong',
                cancelButtonText: 'Chưa, để sau',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280'
            });

            if (!confirmResult.isConfirmed) {
                Swal.fire({
                    icon: 'info',
                    title: 'Hãy chuyển khoản trước!',
                    text: 'Vui lòng hoàn tất chuyển khoản rồi quay lại xác nhận.',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }

            try {
                const res = await fetch(`${apiBase}/khach-thue/hoa-don/${hoaDonId}/xac-nhan-thanh-toan`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '✅ Đã gửi xác nhận thanh toán!',
                    text: 'Chủ trọ sẽ kiểm tra và xác nhận sớm.',
                    confirmButtonColor: '#16a34a'
                });

                const statusBadge = document.querySelector('span.px-3');
                if (statusBadge) {
                    statusBadge.textContent = 'Chờ xác nhận';
                    statusBadge.className = 'px-3 py-1 text-sm rounded-full font-medium bg-amber-100 text-amber-700';
                }

                if (btnQR) btnQR.remove();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message || 'Không thể gửi xác nhận.'
                    });
                }

                modal.classList.add('hidden');
                setTimeout(() => location.reload(), 1500);

            } catch (err) {
                console.error('❌ Lỗi xác nhận thanh toán:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Không thể gửi xác nhận!',
                    text: 'Vui lòng thử lại sau.'
                });
            }
        });
    }
});
</script>

@endsection
