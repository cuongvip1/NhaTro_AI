@extends('layouts.chu-tro')
@section('title', 'Chi tiết bài đăng')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#7c3aed'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#7c3aed'
            });
        </script>
    @endif

    <style>
        body {
            background: #f9fafb;
            font-family: 'Inter', sans-serif;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .post-title {
            font-size: 2rem;
            font-weight: 800;
            color: #111827;
        }

        .post-subinfo {
            color: #6b7280;
            font-size: .95rem;
            margin-top: 4px;
        }

        .back-link {
            color: #6b7280;
            text-decoration: none;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: .2s;
        }

        .back-link:hover {
            color: #7c3aed;
        }

        .post-wrapper {
            display: grid;
            grid-template-columns: 2fr 1.2fr;
            gap: 32px;
        }

        /* ===== Gallery ===== */
        .gallery-wrap {
            display: flex;
            gap: 16px;
        }

        .main-figure {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            height: 420px;
            background: #f3f4f6;
        }

        .main-figure img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease, transform-origin 0.3s ease;
            cursor: zoom-in;
        }

        /* Khi hover vào thì zoom nhẹ lên */
        .main-figure:hover img {
            transform: scale(1.8);
        }

        .thumbs {
            margin-top: 12px;
        }

        .thumbs .swiper-slide {
            width: 90px !important;
            height: 70px !important;
            cursor: pointer;
            opacity: .75;
            border-radius: 10px;
            overflow: hidden;
            transition: .25s;
        }

        .thumbs .swiper-slide-thumb-active {
            opacity: 1;
            border: 2px solid #7c3aed;
        }

        .thumbs img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #7c3aed;
            top: 35%;
            transform: scale(.7);
        }

        .swiper-button-disabled {
            opacity: .3 !important;
        }

        @media (max-width: 992px) {
            .gallery-wrap {
                flex-direction: column;
            }

            .zoom-pane {
                display: none !important;
            }

            .post-wrapper {
                grid-template-columns: 1fr;
            }
        }

        .info-card {
            background: #fff;
            border-radius: 18px;
            padding: 26px 28px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        }

        .price {
            font-size: 2rem;
            font-weight: 800;
            color: #7c3aed;
        }

        .price small {
            font-size: .95rem;
            color: #6b7280;
        }

        .meta {
            color: #4b5563;
            font-size: .95rem;
            margin-top: 6px;
        }

        .status {
            margin-top: 10px;
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: .9rem;
        }

        .status-dang {
            background: #dcfce7;
            color: #166534;
        }

        .status-an {
            background: #f3f4f6;
            color: #4b5563;
        }

        .status-cho_duyet {
            background: #fef9c3;
            color: #854d0e;
        }

        .status-tu_choi {
            background: #fee2e2;
            color: #991b1b;
        }

        /* === BẢNG DỊCH VỤ GỌN GÀNG === */
        .cost-table {
            margin-top: 18px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .cost-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .cost-table th {
            background: #ede9fe;
            color: #5b21b6;
            font-weight: 700;
            padding: 12px 16px;
            font-size: 0.95rem;
            text-align: left;
        }

        .cost-table td {
            padding: 12px 16px;
            font-size: 0.95rem;
            color: #374151;
            border-top: 1px solid #f3f4f6;
        }

        .cost-table tbody tr:hover {
            background: #faf5ff;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 12px;
            margin-top: 22px;
        }

        .action-buttons form,
        .action-buttons a {
            flex: 1;
        }

        .btn-action {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.06);
        }

        /* Màu đồng nhất */
        .btn-edit {
            background: #7c3aed;
            color: #fff;
        }

        .btn-edit:hover {
            background: #6d28d9;
        }

        .btn-toggle {
            background: #fde68a;
            color: #78350f;
        }

        .btn-toggle:hover {
            background: #fcd34d;
        }

        .btn-delete {
            background: #ef4444;
            color: #fff;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        /* Đồng bộ icon và chữ */
        .btn-action i {
            font-size: 1.1rem;
            line-height: 0;
        }

        .desc-box {
            background: #fff;
            margin-top: 36px;
            border-radius: 18px;
            padding: 28px 32px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border: 1px solid #ede9fe;
            position: relative;
        }

        .desc-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #7c3aed, #c084fc);
        }

        .desc-box h5 {
            font-weight: 700;
            color: #4c1d95;
            margin-bottom: 18px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .desc-box p {
            color: #374151;
            line-height: 1.75;
            font-size: 1rem;
        }
    </style>

    <div class="container py-4">
        <div class="header-section">
            <div>
                <div class="post-title">{{ $post->tieu_de }}</div>
                <div class="post-subinfo">
                    <i class="ri-map-pin-line"></i> {{ $post->phong->dayTro->dia_chi ?? 'TP.HCM' }}
                </div>
            </div>
            <a href="{{ route('chu-tro.bai-dang.index') }}" class="back-link">
                <i class="ri-arrow-left-line"></i> Quay lại danh sách
            </a>
        </div>

        <div class="post-wrapper">
            {{-- LEFT --}}
            <div>
                <div class="gallery-wrap">
                    @php
                        $first = $post->anh->first()?->url ?? asset('images/no-image.png');
                    @endphp
                    <div class="main-figure" id="mainFigure">
                        <img id="mainImage" src="{{ $post->anh->first()?->url ?? asset('images/no-image.png') }}"
                            alt="Ảnh phòng" onerror="this.src='{{ asset('images/no-image.png') }}'">
                    </div>

                    <div id="zoomPane" class="zoom-pane"></div>
                </div>

                <div class="swiper thumbs mt-3">
                    <div class="swiper-wrapper">
                        @foreach ($post->anh as $img)
                            <div class="swiper-slide">
                                <img src="{{ $img->url ?? asset('images/no-image.png') }}" alt="Ảnh"
                                    onclick="changeMainImage(this)"
                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'">
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="info-card">
                <div class="price">
                    {{ number_format($post->gia_niem_yet, 0, ',', '.') }}đ
                    <small>/ tháng</small>
                </div>

                <p class="meta"><i class="ri-building-2-line"></i>
                    Dãy: {{ $post->phong->dayTro->ten_day_tro ?? 'N/A' }} |
                    <i class="ri-door-line"></i> Phòng: {{ $post->phong->so_phong }}
                </p>
                <p class="meta"><i class="ri-ruler-line"></i> Diện tích: <b>{{ $post->phong->dien_tich ?? 0 }}</b> m²</p>
                <p class="meta"><i class="ri-group-line"></i> Sức chứa: <b>{{ $post->phong->suc_chua ?? 'Chưa có' }}</b>
                    người</p>
                {{-- 🏬 Tầng --}}
                <p class="meta">
                    <i class="ri-building-line text-purple"></i>
                    Tầng: <b>{{ $post->phong->tang ?? 'Chưa rõ' }}</b>
                </p>

                {{-- 🚪 Trạng thái phòng --}}
                @php
                    $phongStatusMap = [
                        'trong' => ['Phòng trống', '#16a34a'],
                        'da_thue' => ['Đã thuê', '#dc2626'],
                        'bao_tri' => ['Bảo trì', '#f59e0b'],
                    ];
                    [$phongStatusText, $phongStatusColor] = $phongStatusMap[$post->phong->trang_thai ?? 'trong'] ?? ['Không xác định', '#6b7280'];
                @endphp
                <p class="meta">
                    <i class="ri-home-4-line text-purple"></i>
                    Tình trạng: <b style="color: {{ $phongStatusColor }}">{{ $phongStatusText }}</b>
                </p>

                @php
                    $statusMap = [
                        'dang' => ['Đang hiển thị', 'status-dang'],
                        'an' => ['Đã ẩn', 'status-an'],
                        'cho_duyet' => ['Chờ duyệt', 'status-cho_duyet'],
                        'tu_choi' => ['Từ chối', 'status-tu_choi'],
                    ];
                    [$statusText, $statusClass] = $statusMap[$post->trang_thai] ?? ['Không xác định', 'status-an'];
                @endphp
                <span class="status {{ $statusClass }}">{{ $statusText }}</span>

                {{-- 💡 Dịch vụ --}}
                @if ($post->phong->dichVuDinhKy->count())
                    <div class="cost-table mt-3">
                        <table>
                            <thead>
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th>Đơn giá</th>
                                    <th>Đơn vị</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($post->phong->dichVuDinhKy as $dv)
    @if ($dv->dichVu)
        <tr>
            <td>{{ $dv->dichVu->ten }}</td>
            <td>{{ number_format($dv->don_gia ?? 0) }}đ</td>
            <td>{{ $dv->dichVu->don_vi }}</td>
        </tr>
    @endif
@endforeach

                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="action-buttons">
                    {{-- Sửa --}}
                    <a href="{{ route('chu-tro.bai-dang.edit', $post->id) }}" class="btn-action btn-edit">
                        <i class="ri-edit-line"></i> Sửa
                    </a>

                    @php
                        $user = session('user');
                        $isOwner = isset($user['id']) && isset($post->phong->dayTro) && $user['id'] === $post->phong->dayTro->chu_tro_id;
                        $isAdmin = isset($user['vai_tro']) && $user['vai_tro'] === 'admin';
                    @endphp

                    {{-- Ẩn / Hiển thị (chỉ chủ trọ của bài hoặc admin) --}}
                    @if($isOwner || $isAdmin)
                        <form action="{{ route('chu-tro.bai-dang.toggle', $post->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action btn-toggle">
                                <i class="ri-eye-line"></i>
                                {{ $post->trang_thai === 'dang' ? 'Ẩn bài' : 'Hiển thị' }}
                            </button>
                        </form>
                    @endif

                    {{-- Xóa (chỉ chủ trọ hoặc admin được phép) --}}
                    @if($isOwner || $isAdmin)
                        <form id="deleteForm" action="{{ route('chu-tro.bai-dang.destroy', $post->id) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete">
                                <i class="ri-delete-bin-line"></i> Xóa
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="desc-box">
            <h5><i class="ri-align-left"></i> Mô tả chi tiết</h5>
            <p>
                @foreach (explode('.', $post->mo_ta ?? 'Chưa có mô tả.') as $line)
                    @if (trim($line) != '')
                        <span>{{ trim($line) }}.</span>
                    @endif
                @endforeach
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const thumbs = new Swiper('.thumbs', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }
        });

        document.getElementById('btnDelete')?.addEventListener('click', () => {
            if (confirm('Xóa bài đăng này? Hành động không thể hoàn tác.')) {
                document.getElementById('deleteForm').submit();
            }
        });
        const mainImage = document.getElementById('mainImage');
        const figure = document.getElementById('mainFigure');

        // Khi rê chuột vào ảnh
        figure.addEventListener('mousemove', e => {
            const { left, top, width, height } = figure.getBoundingClientRect();
            const x = ((e.clientX - left) / width) * 100;
            const y = ((e.clientY - top) / height) * 100;
            mainImage.style.transformOrigin = `${x}% ${y}%`;
            mainImage.style.transform = 'scale(1.8)';
        });

        // Khi rời chuột khỏi ảnh
        figure.addEventListener('mouseleave', () => {
            mainImage.style.transformOrigin = 'center center';
            mainImage.style.transform = 'scale(1)';
        });
    </script>
    <script>
        document.getElementById('btnDelete')?.addEventListener('click', e => {
            Swal.fire({
                title: 'Xóa bài đăng?',
                text: 'Hành động này không thể hoàn tác!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xóa ngay',
                cancelButtonText: 'Hủy'
            }).then(result => {
                if (result.isConfirmed) document.getElementById('deleteForm').submit();
            });
        });
    </script>
    <script>
        document.querySelectorAll('.toggleForm').forEach(f => {
            f.addEventListener('submit', () => {
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ giây lát.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            });
        });
    </script>

@endsection