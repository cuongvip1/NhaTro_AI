@extends('layouts.chu-tro')

@section('title', 'Chi tiết hợp đồng thuê')

@section('content')
    <div class="max-w-5xl mx-auto p-6 space-y-8">
        {{-- Tiêu đề & nút quay lại --}}
        <div class="flex items-center justify-between border-b pb-4">
            <h1 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="ri-file-text-line text-3xl"></i> Chi tiết hợp đồng thuê
            </h1>
            <a href="{{ route('chu-tro.hop-dong.index') }}"
                class="text-gray-600 hover:text-indigo-600 flex items-center gap-1">
                <i class="ri-arrow-go-back-line"></i> Quay lại
            </a>
        </div>

        {{-- Thông tin hợp đồng --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-6 ring-1 ring-gray-900/5">

            {{-- Thông tin chính --}}
            <div class="space-y-3 text-gray-700 dark:text-gray-300">
                <p><b>🏠 Phòng:</b> {{ $hop_dong['so_phong'] ?? '' }} — {{ $hop_dong['ten_day_tro'] ?? '' }}</p>
                <p><b>👤 Khách thuê:</b> {{ $hop_dong['khach_thue'] ?? '' }}</p>
                <p><b>📧 Email:</b> {{ $hop_dong['email'] ?? 'Không có' }}</p>
                <p><b>📞 SĐT:</b> {{ $hop_dong['so_dien_thoai'] ?? 'Không có' }}</p>
                <p><b>🪪 CCCD:</b> {{ $hop_dong['cccd'] ?? 'Chưa cung cấp' }}</p>

                @php
                    $yc = $hop_dong['yeu_cau'] ?? null;
                @endphp

                <p><b>📅 Ngày bắt đầu thuê:</b>
                    {{ \Carbon\Carbon::parse($yc['ngay_bat_dau'] ?? $hop_dong['ngay_bat_dau'])->format('d/m/Y') }}
                </p>
                <p><b>📅 Ngày kết thúc thuê:</b>
                    {{ \Carbon\Carbon::parse($yc['ngay_ket_thuc'] ?? $hop_dong['ngay_ket_thuc'])->format('d/m/Y') }}
                </p>

                <p><b>💰 Tiền cọc:</b>
                    {{ number_format($yc['tien_coc'] ?? $hop_dong['tien_coc'] ?? 0, 0, ',', '.') }}đ
                </p>

                <p><b>📝 Ghi chú:</b> {{ $yc['ghi_chu'] ?? $hop_dong['ghi_chu'] ?? 'Không có ghi chú' }}</p>

                {{-- Người thân --}}
                <div>
                    <p><b>👨‍👩‍👧 Người thân sống cùng:</b></p>
                    @php
                        $nguoiThanList = $yc['nguoi_than'] ?? $hop_dong['nguoi_than'] ?? [];
                        if (is_string($nguoiThanList)) {
                            $decoded = json_decode($nguoiThanList, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $nguoiThanList = $decoded;
                            } else {
                                $nguoiThanList = $nguoiThanList ? [['ten' => $nguoiThanList]] : [];
                            }
                        }
                    @endphp

                    @if (!empty($nguoiThanList))
                        <ul class="list-disc ml-6 space-y-1">
                            @foreach ($nguoiThanList as $nt)
                                <li>
                                    <b>{{ $nt['ho_ten'] ?? ($nt['ten'] ?? 'Ẩn danh') }}</b>
                                    — {{ $nt['moi_quan_he'] ?? 'Không rõ' }}
                                    (📞 {{ $nt['so_dien_thoai'] ?? 'Chưa có' }})
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="ml-6 text-gray-500 italic">Không có người thân đi cùng.</p>
                    @endif
                </div>

                {{-- File hợp đồng --}}
                @php
                    $fileHopDong = $yc['file_hop_dong'] ?? $hop_dong['url_file_hop_dong'] ?? null;
                @endphp
                @if (!empty($fileHopDong))
                    @php
                        $pdfUrl = rtrim(env('API_BASE_URL', 'http://127.0.0.1:8000'), '/') .
                            '/storage/' .
                            ltrim($fileHopDong, '/');
                    @endphp
                    <div class="mt-3">
                        <p><b>📎 File hợp đồng:</b></p>
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

                {{-- Ngày tạo & Trạng thái --}}
                <p><b>⏰ Ngày tạo hợp đồng:</b>
                    {{ \Carbon\Carbon::parse($hop_dong['ngay_tao'])->format('d/m/Y H:i') ?? '--' }}
                </p>

                <p><b>📌 Trạng thái:</b>
                    @php
                        $st = $hop_dong['trang_thai'] ?? 'cho_duyet';
                        $badgeClass = match ($st) {
                            'hieu_luc' => 'bg-green-100 text-green-700',
                            'ket_thuc' => 'bg-gray-100 text-gray-700',
                            'huy' => 'bg-red-100 text-red-700',
                            default => 'bg-yellow-100 text-yellow-700',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $badgeClass }}">
                        {{ str_replace('_', ' ', ucfirst($st)) }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Nút thao tác --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('chu-tro.hop-dong.edit', $hop_dong['id']) }}"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow transition">
                <i class="ri-edit-2-line"></i> Sửa hợp đồng
            </a>
            <form action="{{ route('chu-tro.hop-dong.destroy', $hop_dong['id']) }}" method="POST"
                onsubmit="return confirm('Bạn có chắc chắn muốn xóa hợp đồng này không?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow transition">
                    <i class="ri-delete-bin-6-line"></i> Xóa
                </button>
            </form>
        </div>
    </div>
@endsection