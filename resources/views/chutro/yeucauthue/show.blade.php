@extends('layouts.chu-tro')

@section('title', 'Chi tiết yêu cầu thuê')

@section('content')
    <div class="max-w-4xl mx-auto p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
        <h1 class="text-2xl font-bold text-indigo-600 mb-6 flex items-center gap-2">
            <i class="ri-file-list-line"></i> Chi tiết yêu cầu thuê
        </h1>

        {{-- ===========================
        🏠 Thông tin chi tiết yêu cầu
        ============================ --}}
        <div class="space-y-3 text-gray-700 dark:text-gray-300">
            <p><b>🏠 Phòng:</b> {{ $yeu_cau['so_phong'] ?? '' }} — {{ $yeu_cau['ten_day_tro'] ?? '' }}</p>
            <p><b>👤 Khách thuê:</b> {{ $yeu_cau['khach_thue'] ?? '' }}</p>
            <p><b>📧 Email:</b> {{ $yeu_cau['email'] ?? 'Không có' }}</p>
            <p><b>🪪 Số CCCD:</b> {{ $yeu_cau['cccd'] ?? 'Không cung cấp' }}</p>

            <p><b>📅 Ngày bắt đầu thuê:</b>
                {{ !empty($yeu_cau['ngay_bat_dau']) ? \Carbon\Carbon::parse($yeu_cau['ngay_bat_dau'])->format('d/m/Y') : '--' }}
            </p>
            <p><b>📅 Ngày kết thúc thuê:</b>
                {{ !empty($yeu_cau['ngay_ket_thuc']) ? \Carbon\Carbon::parse($yeu_cau['ngay_ket_thuc'])->format('d/m/Y') : '--' }}
            </p>

            <p><b>💰 Tiền cọc:</b> {{ number_format($yeu_cau['tien_coc'] ?? 0, 0, ',', '.') }}đ</p>

            <p><b>📝 Ghi chú:</b> {{ $yeu_cau['ghi_chu'] ?? 'Không có ghi chú' }}</p>

            @php
                $nguoiThanList = $yeu_cau['nguoi_than'] ?? null;

                // 🧩 Nếu là chuỗi, thử decode nhiều lần
                if (is_string($nguoiThanList)) {
                    $decoded = json_decode($nguoiThanList, true);
                    if (is_string($decoded)) {
                        $decoded = json_decode($decoded, true);
                    }

                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $nguoiThanList = $decoded;
                    } else {
                        $nguoiThanList = trim($nguoiThanList) !== '' ? [['ho_ten' => $nguoiThanList]] : [];
                    }
                }

                if (!is_array($nguoiThanList)) {
                    $nguoiThanList = [];
                }
            @endphp


            <div class="mt-3">
                <b>👨‍👩‍👧 Người thân sống cùng:</b>
                @if(!empty($nguoiThanList))
                    <ul class="list-disc ml-6 mt-2 space-y-1">
                        @foreach($nguoiThanList as $nt)
                            <li>
                                <span class="font-medium">{{ $nt['ho_ten'] ?? $nt['ten'] ?? 'Không rõ tên' }}</span>
                                — {{ $nt['moi_quan_he'] ?? 'Chưa rõ mối quan hệ' }}
                                @if(!empty($nt['so_dien_thoai']))
                                    (📞 {{ $nt['so_dien_thoai'] }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="italic text-gray-500 mt-1">Không có người thân đi cùng.</p>
                @endif
            </div>

            {{-- ===========================
            📎 File hợp đồng (do khách upload)
            ============================ --}}
            @if(!empty($yeu_cau['file_hop_dong']))
@php
    $pdfUrl =
        rtrim(env('API_BASE_URL', 'http://127.0.0.1:8000'), '/')
        . '/api/chu-tro/yeu-cau-thue/'
        . $yeu_cau['id']
        . '/xem-hop-dong?token='
        . session('api_token');
@endphp

                <div class="mt-3">
                    <p><b>📎 File hợp đồng khách tải lên:</b></p>
                    <div class="flex items-center gap-3 mt-2">
                        <button type="button" id="btn-open-pdf" data-url="{{ $pdfUrl }}"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">
                            👁 Xem hợp đồng
                        </button>

                        <a href="{{ $pdfUrl }}" target="_blank"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 font-medium">
                            ⤓ Mở trong tab mới
                        </a>
                    </div>
                </div>

                {{-- Modal xem PDF --}}
                <div id="pdf-modal" class="hidden fixed inset-0 bg-black/70 z-50 items-center justify-center p-4">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-5xl h-[80vh] relative flex flex-col">
                        <button id="btn-close-pdf"
                            class="absolute top-3 right-3 bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded font-semibold">
                            ✖
                        </button>
                        <iframe id="pdf-frame" src="" class="w-full h-full rounded-b-2xl"></iframe>
                    </div>
                </div>

                <script>
                    const btnOpen = document.getElementById('btn-open-pdf');
                    const modal = document.getElementById('pdf-modal');
                    const iframe = document.getElementById('pdf-frame');
                    const btnClose = document.getElementById('btn-close-pdf');

                    btnOpen?.addEventListener('click', () => {
                        iframe.src = btnOpen.dataset.url;
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });

                    function closePdf() {
                        iframe.src = '';
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }

                    btnClose?.addEventListener('click', closePdf);
                    modal?.addEventListener('click', (e) => {
                        if (e.target === modal) closePdf();
                    });
                </script>
            @else
                <p><b>📎 File hợp đồng:</b>
                    <span class="text-gray-500 italic">Chưa có file tải lên</span>
                </p>
            @endif

            <p><b>⏰ Ngày gửi yêu cầu:</b>
                {{ !empty($yeu_cau['ngay_tao']) ? \Carbon\Carbon::parse($yeu_cau['ngay_tao'])->format('d/m/Y H:i') : '--' }}
            </p>

            <p><b>📌 Trạng thái:</b>
                @php
                    $st = $yeu_cau['trang_thai'] ?? 'cho_duyet';
                    $badgeClass = match ($st) {
                        'cho_duyet' => 'bg-yellow-100 text-yellow-700',
                        'chap_nhan' => 'bg-blue-100 text-blue-700',
                        'da_tao_hop_dong' => 'bg-green-100 text-green-700',
                        'tu_choi' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $badgeClass }}">
                    {{ str_replace('_', ' ', ucfirst($st)) }}
                </span>
            </p>
        </div>

        {{-- ===========================
        ⚙️ Nút thao tác cho chủ trọ
        ============================ --}}
        <div class="mt-8 flex gap-4">
            @if(($yeu_cau['trang_thai'] ?? '') === 'cho_duyet')
                {{-- ✅ Chấp nhận --}}
                <form action="{{ route('chu-tro.yeu-cau-thue.chap-nhan', $yeu_cau['id']) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition"
                        onclick="return confirm('Bạn có chắc muốn chấp nhận và tạo hợp đồng cho yêu cầu này?')">
                        ✅ Chấp nhận & Tạo hợp đồng
                    </button>
                </form>

                {{-- ❌ Từ chối --}}
                <form action="{{ route('chu-tro.yeu-cau-thue.tu-choi', $yeu_cau['id']) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition"
                        onclick="return confirm('Bạn có chắc muốn từ chối yêu cầu này?')">
                        ❌ Từ chối yêu cầu
                    </button>
                </form>
            @elseif(($yeu_cau['trang_thai'] ?? '') === 'chap_nhan')
                <span class="text-blue-600 font-medium">Đã chấp nhận — chờ hợp đồng</span>
            @elseif(($yeu_cau['trang_thai'] ?? '') === 'da_tao_hop_dong')
                <span class="text-green-600 font-medium">Đã tạo hợp đồng</span>
            @elseif(($yeu_cau['trang_thai'] ?? '') === 'tu_choi')
                <span class="text-red-600 font-medium">Đã từ chối</span>
            @endif

            {{-- 🔙 Quay lại --}}
            <a href="{{ route('chu-tro.yeu-cau-thue.index') }}"
                class="ml-auto px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">
                ⬅ Quay lại danh sách
            </a>
        </div>
    </div>
@endsection