@extends('layouts.chu-tro')

@section('title', 'Quản lý bài đăng')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <style>
        body {
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .page-header h4 {
            font-weight: 700;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-header h4 i {
            color: #7c3aed;
        }

        .btn-create {
            background: #7c3aed;
            color: #fff;
            border-radius: 25px;
            font-weight: 500;
            padding: 8px 18px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-create:hover {
            background: #5b21b6;
            transform: translateY(-1px);
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 28px;
        }

        .search-box {
            flex: 1;
            min-width: 240px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 36px 10px 14px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            font-size: 0.95rem;
            transition: 0.3s;
        }

        .search-box input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .search-box i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .filter-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-buttons button {
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            background: #f3f4f6;
            color: #4b5563;
            transition: 0.3s;
        }

        .filter-buttons button.active {
            background: #7c3aed;
            color: #fff;
        }

        .filter-buttons button:hover {
            background: #ede9fe;
            color: #4c1d95;
        }

        .listing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .listing-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
            transition: 0.3s;
        }

        .listing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 28px rgba(124, 58, 237, 0.15);
        }

        .listing-image {
            width: 100%;
            height: 190px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }

        .status-badge {
            position: absolute;
            top: 12px;
            left: 14px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 4px 10px;
            color: #fff;
        }

        .status-dang {
            background: #16a34a;
        }

        .status-an {
            background: #6b7280;
        }

        .status-cho_duyet {
            background: #f59e0b;
        }

        .status-tu_choi {
            background: #ef4444;
        }

        .view-count {
            position: absolute;
            top: 12px;
            right: 14px;
            font-size: 0.85rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 4px;
            background: #f9fafb;
            border-radius: 10px;
            padding: 3px 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .area-tag {
            position: absolute;
            bottom: 10px;
            left: 14px;
            background: #7c3aed;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        .listing-body {
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 230px;
        }

        .listing-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 6px;
            color: #111827;
        }

        .listing-location {
            font-size: 0.9rem;
            color: #6b7280;
            display: flex;
            align-items: center;
        }

        .listing-location i {
            margin-right: 5px;
            color: #7c3aed;
        }

        .tag {
            display: inline-block;
            background: #f3f4f6;
            color: #555;
            border-radius: 8px;
            padding: 3px 10px;
            font-size: 0.75rem;
            margin-right: 6px;
            margin-top: 4px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .price {
            color: #4c1d95;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .price small {
            color: #6b7280;
            font-weight: 400;
            font-size: 0.85rem;
        }

        .btn-purple {
            background: #7c3aed;
            color: #fff;
            border-radius: 10px;
            font-weight: 500;
            padding: 6px 14px;
            font-size: 0.9rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-purple:hover {
            background: #5b21b6;
            transform: translateY(-1px);
        }

        .bottom-actions {
            border-top: 1px solid #f3f4f6;
            padding: 12px 16px;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .bottom-actions button {
            border: none;
            background: #f9fafb;
            padding: 8px 14px;
            border-radius: 10px;
            color: #6b7280;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.3s;
            cursor: pointer;
        }

        .bottom-actions button:hover {
            background: #ede9fe;
            color: #4c1d95;
        }

        .listing-card.inactive {
            opacity: 0.7;
            filter: grayscale(0.2);
        }
    </style>

    <div class="container py-4">
        <div class="page-header">
            <h4><i class="ri-file-list-line"></i> Danh sách bài đăng</h4>
            <a href="{{ route('chu-tro.bai-dang.create') }}" class="btn btn-create">
                <i class="ri-add-line"></i> Tạo bài đăng
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" class="filter-bar">
            <div class="search-box">
                <input type="text" name="search" placeholder="🔍 Tìm bài đăng, địa chỉ hoặc mô tả..."
                    value="{{ request('search') }}">
                <i class="ri-search-line"></i>
            </div>

            <div class="filter-buttons">
                @php
                    $status = request('status');
                    $statuses = [
                        null => 'Tất cả',

                        'dang' => 'Đang hiển thị',
                        'an' => 'Đã ẩn',
                        'cho_duyet' => 'Chờ duyệt',
                        'tu_choi' => 'Từ chối',
                    ];
                @endphp
                @foreach ($statuses as $key => $label)
                    <button type="submit" name="status" value="{{ $key }}" class="{{ $status === $key ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </form>

        @if ($posts->isEmpty())
            <div class="text-center py-5 text-muted">
                <p><i class="ri-file-warning-line fs-4 d-block mb-2"></i>Không tìm thấy bài đăng nào.</p>
            </div>
        @else
            <div class="listing-grid">
                @foreach ($posts as $post)
                    @php

                        $img = $post->anh->first()?->url ?? asset('images/no-image.png');

                        $statusClass = [
                            'dang' => 'status-dang',
                            'an' => 'status-an',
                            'cho_duyet' => 'status-cho_duyet',
                            'tu_choi' => 'status-tu_choi',
                        ][$post->trang_thai] ?? 'status-an';

                        $diaChi = $post->dia_chi ?? ($post->phong->dayTro->dia_chi ?? 'Chưa có địa chỉ');
                    @endphp
                    <div class="listing-card {{ $post->trang_thai !== 'dang' ? 'inactive' : '' }}">
                        <div style="position: relative;">
                            <img src="{{ $post->anh->first()?->url ?? asset('images/no-image.png') }}" alt="Ảnh bài đăng"
                                class="listing-image" onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'">
                            <div class="status-badge {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $post->trang_thai)) }}
                            </div>
                            <div class="view-count"><i class="ri-eye-line"></i> {{ $post->luot_xem ?? rand(50, 150) }}</div>
                            <div class="area-tag">{{ $post->phong->dien_tich ?? 20 }}m²</div>
                        </div>

                        <div class="listing-body">
                            <div>
                                <h5 class="listing-title">{{ $post->tieu_de }}</h5>
                                <p class="listing-location mb-1">
                                    <i class="ri-map-pin-line"></i> {{ $diaChi }}
                                </p>

                                <div class="mb-2">
                                    @forelse ($post->phong->dichVuDinhKy->take(3) as $dv)
                                        <span class="tag">{{ $dv->dichVu->ten }}
                                            ({{ number_format($dv->dichVu->don_gia, 0, ',', '.') }}/{{ $dv->dichVu->don_vi }})</span>
                                    @empty
                                        <span class="text-muted small">Chưa có dịch vụ</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="price-row">
                                <div class="price">
                                    {{ number_format($post->gia_niem_yet, 0, ',', '.') }}đ
                                    <small>/ tháng</small>
                                </div>
                                <a href="{{ route('chu-tro.bai-dang.show', $post->id) }}"
                                    class="btn btn-purple {{ $post->trang_thai !== 'dang' ? 'disabled' : '' }}">
                                    <i class="ri-eye-line"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>

                        <div class="bottom-actions">
                            <button type="button"><i class="ri-phone-line"></i> Gọi</button>
                            <button type="button"><i class="ri-chat-1-line"></i> Nhắn tin</button>

                            {{-- 🏠 Nút Đặt phòng --}}
                            <form action="{{ route('khach-thue.yeu-cau-thue.store') }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn gửi yêu cầu thuê phòng này không?')"
                                style="display:inline;">
                                @csrf
                                <input type="hidden" name="bai_dang_id" value="{{ $post->id }}">
                                <input type="hidden" name="ghi_chu" value="Tôi quan tâm đến phòng này.">
                                <button type="submit">
                                    <i class="ri-calendar-line"></i> Đặt phòng
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $posts->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection