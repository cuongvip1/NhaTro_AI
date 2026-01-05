@extends('layouts.tenant-layout')

@section('title', 'Trang chủ')
@section('page_title', 'Bảng điều khiển')

@section('tenant_content')

    {{-- <div id="recommendation-section" class="mt-6">
        <div id="recommendation-card" class="hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Gợi ý phòng cho bạn</h3>
            <div id="recommendation-content" class="mt-4 text-gray-700 dark:text-gray-300">Đang tải đề xuất...</div>
        </div>
    </div>
 --}}
    
    {{-- ===== THỐNG KÊ TỔNG QUAN ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Hợp đồng --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="text-gray-500 text-sm">Số hợp đồng đang thuê</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">
                {{ count($hopDong ?? []) }}
            </p>
        </div>

        {{-- Hóa đơn --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="text-gray-500 text-sm">Hóa đơn chưa thanh toán</h3>
            <p class="text-3xl font-bold text-yellow-500 mt-2">
                {{ collect($hoaDon ?? [])->where('trang_thai', 'chua_thanh_toan')->count() }}
            </p>
        </div>

        {{-- Thông báo --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="text-gray-500 text-sm">Thông báo mới</h3>
            <p class="text-3xl font-bold text-red-500 mt-2">
                {{ collect($thongBao ?? [])->where('da_xem', 0)->count() }}
            </p>
        </div>
    </div>

    {{-- ===== HỢP ĐỒNG HIỆN TẠI ===== --}}
    @if (!empty($hopDong))
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <i class="ri-file-text-line text-indigo-600"></i> Hợp đồng đang hiệu lực
                </h2>
                <a href="{{ route('khach-thue.hop-dong.index') }}" class="text-sm text-indigo-600 hover:underline">Xem tất
                    cả</a>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($hopDong as $hd)
                    @php
                        $phong = $hd['phong'] ?? [];
                        $dayTro = $phong['day_tro'] ?? [];
                    @endphp
                    <div class="py-4 flex items-center justify-between">
                        <div>
                            <p class="text-gray-800 dark:text-gray-100">
                                Bạn đang thuê <strong>{{ $phong['so_phong'] ?? 'Phòng trọ' }}</strong>
                                tại <strong>{{ $dayTro['ten_day_tro'] ?? 'Dãy trọ' }}</strong>.
                            </p>
                            <p class="text-gray-500 text-sm mt-1">
                                Thời hạn:
                                {{ $hd['ngay_bat_dau'] ? \Carbon\Carbon::parse($hd['ngay_bat_dau'])->format('d/m/Y') : 'N/A' }}
                                –
                                {{ $hd['ngay_ket_thuc'] ? \Carbon\Carbon::parse($hd['ngay_ket_thuc'])->format('d/m/Y') : 'N/A' }}
                            </p>
                        </div>
                        <a href="{{ route('khach-thue.hop-dong.show', $hd['id']) }}"
                            class="text-indigo-600 text-sm font-medium hover:underline">
                            Chi tiết
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow text-gray-500 text-center">
            <i class="ri-home-2-line text-3xl mb-2 text-gray-400"></i>
            <p>Bạn hiện chưa có hợp đồng thuê trọ nào.</p>
        </div>
    @endif

    {{-- ===== HÓA ĐƠN & THÔNG BÁO ===== --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- 💳 Hóa đơn sắp đến hạn --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <i class="ri-bill-line text-indigo-500"></i> Hóa đơn sắp đến hạn
                </h3>
                <a href="{{ route('khach-thue.hoa-don.index') }}" class="text-sm text-indigo-600 hover:underline">Xem tất
                    cả</a>
            </div>

            @php
                $hoaDonSapDenHan = collect($hoaDon ?? [])->where('trang_thai', 'chua_thanh_toan')->take(3);
            @endphp

            @forelse ($hoaDonSapDenHan as $hd)
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-3 last:border-0">
                    <p class="text-gray-800 dark:text-gray-100 font-medium">
                        {{ $hd['ma_hoa_don'] ?? ('HĐ-' . $hd['id']) }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Số tiền:
                        <strong class="text-gray-800">
                            {{ number_format($hd['tong_tien'] ?? 0, 0, ',', '.') }}đ
                        </strong>
                    </p>
                    <p class="text-xs text-red-500 mt-1">
                        Hạn thanh toán:
                        {{ $hd['han_thanh_toan'] ? \Carbon\Carbon::parse($hd['han_thanh_toan'])->format('d/m/Y') : 'N/A' }}
                    </p>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Không có hóa đơn nào sắp đến hạn.</p>
            @endforelse
        </div>

        {{-- 🔔 Thông báo gần đây --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <i class="ri-notification-3-line text-indigo-500"></i> Thông báo gần đây
                </h3>
                <a href="{{ route('khach-thue.thong-bao.index') }}" class="text-sm text-indigo-600 hover:underline">Xem tất
                    cả</a>
            </div>

            @forelse (collect($thongBao ?? [])->take(3) as $tb)
                <div class="flex items-start space-x-3 border-b border-gray-100 dark:border-gray-700 pb-3 mb-3 last:border-0">
                    <i class="ri-notification-3-line text-indigo-500 text-lg mt-1"></i>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $tb['tieu_de'] ?? 'Thông báo mới' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">{{ $tb['noi_dung'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $tb['ngay_tao'] ? \Carbon\Carbon::parse($tb['ngay_tao'])->diffForHumans() : '' }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Không có thông báo nào mới.</p>
            @endforelse
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const recCard = document.getElementById('recommendation-card');
    const recContent = document.getElementById('recommendation-content');

    // Try to build tenant data from authenticated user if available
    const user = @json(auth()->user());

    const payload = {
        gia_phong: null,
        danh_gia_sao: user && user.rating ? user.rating : 4,
        so_nguoi_o: user && user.so_nguoi_o ? user.so_nguoi_o : 1,
        dien_tich: user && user.preferred_area ? user.preferred_area : 25,
        dia_chi_quan: user && user.dia_chi_quan ? user.dia_chi_quan : null,
        dich_vu: '',
        tien_ich: ''
    };

    // Call recommendation API
    fetch('/api/khach-thue/recommend', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    }).then(r => r.json()).then(async data => {
        if (!data.success) {
            recContent.textContent = 'Không thể lấy đề xuất: ' + (data.message || 'Lỗi');
            recCard.classList.remove('hidden');
            return;
        }

        const rec = data.recommendation || {};
        const pid = rec.recommended_phong_id;
        if (!pid) {
            recContent.textContent = 'Hiện chưa có phòng gợi ý phù hợp.';
            recCard.classList.remove('hidden');
            return;
        }

        // fetch room details from public API
        try {
            const r2 = await fetch('/api/phong/' + pid, { credentials: 'same-origin' });
            if (!r2.ok) throw new Error('Không lấy được thông tin phòng');
            const room = await r2.json();

            // Render a simple card
            recContent.innerHTML = `
                <div class="flex items-center gap-4">
                    <div class="w-24 h-20 bg-gray-100 rounded overflow-hidden">
                        ${room.anh && room.anh.length ? `<img src="${room.anh[0]}" alt="" class="w-full h-full object-cover">` : ''}
                    </div>
                    <div>
                        <a href="/phong/${pid}" class="text-indigo-600 font-semibold">${room.tieu_de || ('Phòng ' + pid)}</a>
                        <div class="text-sm text-gray-500 mt-1">${room.dia_chi_quan || ''} • ${room.gia_phong ? (room.gia_phong.toLocaleString() + 'đ/tháng') : ''}</div>
                    </div>
                </div>
            `;
            recCard.classList.remove('hidden');
        } catch (e) {
            recContent.textContent = 'Đã xảy ra lỗi khi lấy chi tiết phòng.';
            recCard.classList.remove('hidden');
            console.error(e);
        }

    }).catch(err => {
        recContent.textContent = 'Lỗi khi gọi API đề xuất.';
        recCard.classList.remove('hidden');
        console.error(err);
    });
});
</script>
@endpush