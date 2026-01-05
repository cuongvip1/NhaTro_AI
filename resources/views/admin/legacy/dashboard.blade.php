@extends('admin.layout')

@section('title', 'Admin - Dashboard')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Tổng quan</h1>
            <p class="text-sm text-slate-500 mt-1">Bảng điều khiển quản trị — theo dõi hoạt động, bài đăng và thống kê nhanh.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-sm shadow-sm hover:bg-gray-50">Xuất báo cáo</button>
            <a href="{{ route('admin.posts') }}" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:opacity-95">Quản lý bài viết</a>
        </div>
    </div>

    <!-- Top stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $s = $stats ?? [];
            $recent = $recentPosts ?? [];
        @endphp

        <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <p class="text-sm text-slate-500">Bài chờ duyệt</p>
                    <p class="text-2xl font-semibold mt-1">{{ $s['dang_cho_duyet'] ?? ($s['pending'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-amber-50 grid place-items-center text-amber-600">
                    <i class="ri-time-line text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-400">Trong hệ thống</div>
        </div>

        <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <p class="text-sm text-slate-500">Đã duyệt (tháng)</p>
                    <p class="text-2xl font-semibold mt-1">{{ $s['da_duyet_thang_nay'] ?? ($s['approved_this_month'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-emerald-50 grid place-items-center text-emerald-600">
                    <i class="ri-check-line text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-400">So với tháng trước: <span class="font-medium">+12%</span></div>
        </div>

        <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <p class="text-sm text-slate-500">Người dùng</p>
                    <p class="text-2xl font-semibold mt-1">{{ $s['users'] ?? ($s['nguoi_dung'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-indigo-50 grid place-items-center text-indigo-600">
                    <i class="ri-team-line text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-400">Active trong 30 ngày</div>
        </div>

        <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <p class="text-sm text-slate-500">Bài viết / tháng</p>
                    <p class="text-2xl font-semibold mt-1">{{ $s['posts_this_month'] ?? ($s['posts_month'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-sky-50 grid place-items-center text-sky-600">
                    <i class="ri-bar-chart-line text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-400">Xu hướng</div>
        </div>
    </div>

    <!-- Charts & recent -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold">Hoạt động (7 ngày)</h2>
                <div class="text-sm text-slate-500">Cập nhật tự động</div>
            </div>

            <!-- simple sparkline using SVG (data is cosmetic if no $s['spark'] given) -->
            @php
                $spark = $s['spark'] ?? [5,8,6,10,9,12,11];
                $max = max($spark);
            @endphp
            <svg class="w-full h-28" viewBox="0 0 140 40" preserveAspectRatio="none">
                <polyline fill="none" stroke="#6366F1" stroke-width="2" points="@php
                    echo implode(' ', array_map(function($i,$idx) use($spark,$max){ $x = ($idx)*(140/(count($spark)-1)); $y = 40 - ($i/$max)*34 - 3; return "$x,$y"; }, $spark, array_keys($spark)));
                @endphp" />
            </svg>

            <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-slate-600">
                <div class="space-y-2">
                    <div class="font-medium">Lượt xem: <span class="text-slate-800">{{ $s['views_last_7'] ?? 1240 }}</span></div>
                    <div class="text-xs">Tăng 8% so với tuần trước</div>
                </div>
                <div class="space-y-2 text-right">
                    <div class="font-medium">Tương tác: <span class="text-slate-800">{{ $s['interactions'] ?? 320 }}</span></div>
                    <div class="text-xs">Bình luận & phản hồi</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
            <h3 class="font-semibold mb-3">Bài viết mới</h3>
            <div class="divide-y">
                @if(!empty($recent) && (is_array($recent) || $recent instanceof \Illuminate\Support\Collection))
                    @foreach($recent as $post)
                        <div class="py-3 flex items-start justify-between">
                            <div class="flex-1">
                                <div class="font-medium text-slate-800">{{ is_array($post) ? ($post['title'] ?? ($post['tieu_de'] ?? '—')) : ($post->title ?? ($post->tieu_de ?? '—')) }}</div>
                                <div class="text-xs text-slate-500">{{ is_array($post) ? ($post['author_name'] ?? ($post['nguoi_dang'] ?? '—')) : ($post->author_name ?? ($post->nguoi_dang ?? '—')) }} • {{ \Illuminate\Support\Str::limit(is_array($post) ? ($post['created_at'] ?? ($post['ngay_tao'] ?? '')) : ($post->created_at ?? ($post->ngay_tao ?? '')), 16) }}</div>
                            </div>
                            <div class="ml-3">
                                <span class="px-2 py-1 rounded text-xs bg-amber-100 text-amber-700">{{ is_array($post) ? ($post['status'] ?? ($post['trang_thai'] ?? 'chờ')) : ($post->status ?? ($post->trang_thai ?? 'chờ')) }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach(range(1,5) as $i)
                        <div class="py-3 flex items-start justify-between">
                            <div class="flex-1">
                                <div class="font-medium">Phòng trọ thử nghiệm {{ $i }}</div>
                                <div class="text-xs text-slate-500">Người đăng: user{{ $i }} • 10/{{ 20+$i }}/2025</div>
                            </div>
                            <div class="ml-3">
                                <span class="px-2 py-1 rounded text-xs bg-amber-100 text-amber-700">Chờ duyệt</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Quick stats table -->
    <div class="mt-8 bg-white rounded-xl shadow border border-gray-100 p-5">
        <h3 class="font-semibold mb-4">Thống kê chi tiết</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-slate-500">
                        <th class="py-2">Chỉ số</th>
                        <th class="py-2">Giá trị</th>
                        <th class="py-2">Ghi chú</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="py-2">Tổng bài viết</td>
                        <td class="py-2">{{ $s['total_posts'] ?? 124 }}</td>
                        <td class="py-2">Tổng từ đầu năm</td>
                    </tr>
                    <tr>
                        <td class="py-2">Người dùng mới (30d)</td>
                        <td class="py-2">{{ $s['new_users_30d'] ?? 18 }}</td>
                        <td class="py-2">Đăng ký mới</td>
                    </tr>
                    <tr>
                        <td class="py-2">Bài báo cáo spam</td>
                        <td class="py-2">{{ $s['spam_reports'] ?? 2 }}</td>
                        <td class="py-2">Chờ xử lý</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
