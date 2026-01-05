@extends('admin.layout')

@section('title', 'Admin - Tổng quan')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Tổng quan</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="p-5 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Bài viết trong tháng</p>
                    <p class="text-2xl font-semibold mt-1">{{ $stats['posts_this_month'] ?? $stats['bai_viet_trong_thang'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-indigo-50 grid place-items-center text-indigo-600">
                    <i class="ri-file-list-3-line text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Bài đăng chờ duyệt</p>
                    <p class="text-2xl font-semibold mt-1">{{ $stats['dang_cho_duyet'] ?? $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-amber-50 grid place-items-center text-amber-600">
                    <i class="ri-time-line text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Người dùng</p>
                    <p class="text-2xl font-semibold mt-1">{{ $stats['nguoi_dung'] ?? $stats['users'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-emerald-50 grid place-items-center text-emerald-600">
                    <i class="ri-team-line text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Khu vực</p>
                    <p class="text-2xl font-semibold mt-1">{{ $stats['khu_vuc'] ?? $stats['regions'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-50 grid place-items-center text-blue-600">
                    <i class="ri-map-pin-line text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 lg:col-span-2">
            <h2 class="font-semibold mb-4">Bài viết mới</h2>
            <div class="divide-y">
                @php
                    $items = $recentPosts['data'] ?? [];
                @endphp
                @if(count($items) === 0)
                    <div class="py-3 text-sm text-gray-500">Không có bài viết mới trong tháng.</div>
                @else
                    @foreach ($items as $post)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-medium">{{ $post['tieu_de'] ?? 'Untitled' }}</p>
                                <p class="text-sm text-gray-500">Người đăng: {{ $post['tac_gia'] ?? ($post['nguoi_dung_id'] ?? 'N/A') }} • {{ isset($post['ngay_tao']) ? date('d/m/Y', strtotime($post['ngay_tao'])) : (isset($post['ngay_duyet']) ? date('d/m/Y', strtotime($post['ngay_duyet'])) : '') }}</p>
                            </div>
                            @php
                                $label = $post['trang_thai_label'] ?? ucfirst($post['trang_thai'] ?? '—');
                                $colorClass = $post['trang_thai_color'] ?? 'bg-emerald-100 text-emerald-700';
                            @endphp
                            <span class="px-2.5 py-1 rounded text-xs {{ $colorClass }}">{{ $label }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold mb-4">Thống kê nhanh</h2>
            <ul class="space-y-3 text-sm">
                <li class="flex justify-between"><span>Bài viết</span><strong>{{ $stats['total_posts'] ?? 0 }}</strong></li>
                <li class="flex justify-between"><span>Đã duyệt</span><strong>{{ $stats['approved'] ?? 0 }}</strong></li>
                <li class="flex justify-between"><span>Đang ẩn</span><strong>{{ $stats['hidden'] ?? 0 }}</strong></li>
            </ul>
        </div>
    </div>
@endsection
