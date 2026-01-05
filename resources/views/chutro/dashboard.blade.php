@extends('layouts.chu-tro')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-6 space-y-10 animate-fade-in">

        {{-- 🔹 HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-3xl font-extrabold flex items-center gap-3 text-indigo-600 dark:text-indigo-400">
                <span
                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 text-white shadow-md animate-pulse-slow">
                    <i class="ri-bar-chart-line text-xl"></i>
                </span>
                Bảng điều khiển chủ trọ
            </h1>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <a href="{{ route('chu-tro.hoa-don.export') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-lime-500 text-white font-medium shadow hover:shadow-lg hover:scale-[1.03] transition">
                    <i class="ri-file-excel-2-line text-lg"></i> Xuất báo cáo doanh thu
                </a>

                <a href="{{ route('chu-tro.profile.show') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white font-medium shadow hover:shadow-lg hover:scale-[1.03] transition">
                    <i class="ri-user-settings-line text-lg"></i> Hồ sơ cá nhân
                </a>
            </div>
        </div>

        {{-- 🔹 THỐNG KÊ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            @php
                $cards = [
                    ['label' => 'Dãy trọ', 'value' => $stats['so_day_tro'] ?? 0, 'icon' => 'ri-community-line', 'bg' => 'from-indigo-500 to-sky-500'],
                    ['label' => 'Phòng', 'value' => $stats['so_phong'] ?? 0, 'icon' => 'ri-building-2-line', 'bg' => 'from-blue-500 to-cyan-500'],
                    ['label' => 'Đang thuê', 'value' => $stats['so_phong_dang_thue'] ?? 0, 'icon' => 'ri-door-open-line', 'bg' => 'from-emerald-500 to-lime-500'],
                    ['label' => 'Phòng trống', 'value' => $stats['so_phong_trong'] ?? 0, 'icon' => 'ri-door-closed-line', 'bg' => 'from-amber-500 to-orange-500'],
                    ['label' => 'Đang bảo trì', 'value' => $stats['so_phong_bao_tri'] ?? 0, 'icon' => 'ri-tools-line', 'bg' => 'from-rose-500 to-red-500'],
                    ['label' => 'Doanh thu tháng', 'value' => number_format($stats['doanh_thu_thang'] ?? 0) . ' đ', 'icon' => 'ri-wallet-3-line', 'bg' => 'from-fuchsia-500 to-pink-500'],
                ];
            @endphp

            @foreach($cards as $c)
                <div
                    class="group rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span
                                class="h-10 w-10 rounded-xl flex items-center justify-center text-white bg-gradient-to-tr {{ $c['bg'] }} shadow-md">
                                <i class="{{ $c['icon'] }}"></i>
                            </span>
                            <span class="text-gray-600 dark:text-gray-400 text-sm">{{ $c['label'] }}</span>
                        </div>
                    </div>
                    <div class="mt-3 text-2xl font-semibold text-indigo-700 dark:text-indigo-300 count-up"
                        data-count="{{ preg_replace('/[^0-9]/', '', $c['value']) }}">
                        0
                    </div>
                </div>
            @endforeach
        </div>

        {{-- BIỂU ĐỒ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Doanh thu --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6 relative">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-700 dark:text-gray-200">
                    <i class="ri-line-chart-line text-indigo-500"></i> Doanh thu 6 tháng gần nhất
                </h2>
                <canvas id="chartRevenue" height="120"></canvas>
            </div>

            {{-- Tình trạng phòng --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-700 dark:text-gray-200">
                    <i class="ri-pie-chart-2-line text-indigo-500"></i> Tình trạng phòng
                </h2>
                <canvas id="chartOccupancy" height="120"></canvas>
                <div class="mt-5 grid grid-cols-3 text-center text-sm text-gray-600 dark:text-gray-400">
                    <div><span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span> Đang thuê</div>
                    <div><span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span> Trống</div>
                    <div><span class="inline-block h-2 w-2 rounded-full bg-rose-500"></span> Đang bảo trì</div>
                </div>
            </div>
        </div>

        {{-- DỮ LIỆU GẦN ĐÂY --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Bài đăng gần đây --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-700 dark:text-gray-200">
                    <i class="ri-newspaper-line text-indigo-500"></i> Bài đăng gần đây
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="p-3 text-left">Tiêu đề</th>
                                <th class="p-3 text-right">Giá niêm yết</th>
                                <th class="p-3 text-center">Trạng thái</th>
                                <th class="p-3 text-center">Ngày đăng</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($bai_dang_gan_day as $b)
                                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-900 transition">
                                                        <td class="p-3 font-medium truncate max-w-xs">{{ $b['tieu_de'] }}</td>
                                                        <td class="p-3 text-right text-indigo-600">{{ number_format($b['gia_niem_yet']) }} đ</td>
                                                        <td class="p-3 text-center">
                                                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ match ($b['trang_thai']) {
                                    'duyet' => 'bg-emerald-100 text-emerald-700',
                                    'cho_duyet' => 'bg-amber-100 text-amber-700',
                                    'dang' => 'bg-indigo-100 text-indigo-700',
                                    'an' => 'bg-gray-100 text-gray-700',
                                    default => 'bg-gray-100 text-gray-700'
                                } }}">
                                                                {{ $b['trang_thai'] }}
                                                            </span>
                                                        </td>
                                                        <td class="p-3 text-center">{{ \Carbon\Carbon::parse($b['ngay_tao'])->format('d/m/Y') }}
                                                        </td>
                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">Chưa có bài đăng.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Hoạt động gần đây --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-700 dark:text-gray-200">
                    <i class="ri-time-line text-indigo-500"></i> Hoạt động gần đây
                </h2>
                <ol class="relative border-s border-gray-200 dark:border-gray-700">
                    @forelse($hoat_dong_gan_day as $hd)
                        <li class="mb-6 ms-4">
                            <div class="absolute w-3 h-3 bg-indigo-500 rounded-full mt-1.5 -start-1.5"></div>
                            <time class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($hd->ngay_tao)->diffForHumans() }}
                            </time>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $hd->noi_dung }}</p>
                        </li>
                    @empty
                        <li class="ms-4 text-gray-500">Chưa có hoạt động nào.</li>
                    @endforelse
                </ol>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".count-up").forEach(el => {
                const target = parseInt(el.dataset.count);
                if (!target || isNaN(target)) return;
                let val = 0;
                const duration = 1000;
                const start = performance.now();
                const step = (t) => {
                    const p = Math.min((t - start) / duration, 1);
                    val = Math.floor(p * target);
                    el.textContent = val.toLocaleString('vi-VN');
                    if (p < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            });

            // === BIỂU ĐỒ DOANH THU ===
            const ctx = document.getElementById('chartRevenue').getContext('2d');
            const data = @json($doanh_thu_6_thang ?? []);
            const labels = Object.keys(data);
            const values = Object.values(data);
            const VND = new Intl.NumberFormat('vi-VN');

            // 🧠 Tính phần trăm thay đổi
            let percentChange = null;
            if (values.length >= 2) {
                const prev = values[values.length - 2];
                const current = values[values.length - 1];
                percentChange = prev ? (((current - prev) / prev) * 100).toFixed(1) : 0;
            }

            // 🎨 Gradient + Shadow
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99,102,241,0.45)');
            gradient.addColorStop(0.5, 'rgba(139,92,246,0.25)');
            gradient.addColorStop(1, 'rgba(99,102,241,0.05)');

            const glow = {
                id: 'glowShadow',
                beforeDraw(chart) {
                    const { ctx } = chart;
                    ctx.save();
                    ctx.shadowColor = 'rgba(99,102,241,0.25)';
                    ctx.shadowBlur = 10;
                    ctx.shadowOffsetY = 4;
                },
                afterDraw(chart) { chart.ctx.restore(); }
            };

            // 📊 Chart chính
            const revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.length ? labels.map(m => 'Thg ' + m.split('-')[1]) : ['Không có dữ liệu'],
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: values.length ? values : [0],
                        borderColor: '#6366F1',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#4F46E5',
                    }]
                },
                options: {
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(30,41,59,0.9)',
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: c => ' ' + VND.format(c.parsed.y) + ' đ'
                            }
                        },
                        annotation: percentChange !== null ? {
                            annotations: {
                                labelChange: {
                                    type: 'label',
                                    xValue: labels.length - 1,
                                    yValue: values[values.length - 1],
                                    backgroundColor: percentChange >= 0 ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)',
                                    borderRadius: 6,
                                    borderWidth: 0,
                                    content: (percentChange >= 0 ? '▲ ' : '▼ ') + Math.abs(percentChange) + '%',
                                    color: percentChange >= 0 ? '#059669' : '#DC2626',
                                    font: { weight: 'bold' },
                                    yAdjust: -20
                                }
                            }
                        } : {}
                    },
                    scales: {
                        x: { ticks: { color: '#666' }, grid: { color: 'rgba(0,0,0,0.05)' } },
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => VND.format(v) + ' đ', color: '#666' },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        }
                    },
                    animation: { duration: 1000, easing: 'easeOutQuart' }
                },
                plugins: [glow]
            });

            // === BIỂU ĐỒ TÌNH TRẠNG PHÒNG ===
            const occ = {
                dangThue: {{ (int) ($stats['so_phong_dang_thue'] ?? 0) }},
                trong: {{ (int) ($stats['so_phong_trong'] ?? 0) }},
                baoTri: {{ (int) ($stats['so_phong_bao_tri'] ?? 0) }},
            };

            const occCtx = document.getElementById('chartOccupancy').getContext('2d');

            const occChart = new Chart(occCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Đang thuê', 'Trống', 'Đang bảo trì'],
                    datasets: [{
                        data: [occ.dangThue, occ.trong, occ.baoTri],
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    cutout: '70%',
                    rotation: 0,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 1)',
                            titleColor: '#111827',
                            bodyColor: '#111827',
                            borderColor: '#6366F1',
                            borderWidth: 2,
                            cornerRadius: 10,
                            padding: 12,
                            displayColors: true,
                            usePointStyle: true,
                            titleFont: { weight: 'bold', size: 14 },
                            bodyFont: { weight: '500', size: 13 },
                            caretPadding: 8,
                            caretSize: 6,
                            shadowBlur: 10,
                            shadowColor: 'rgba(0,0,0,0.15)',
                            callbacks: {
                                label: (ctx) => {
                                    const label = ctx.label || '';
                                    const value = ctx.parsed || 0;
                                    return `${label}: ${value} phòng`;
                                }
                            }
                        }

                    },
                    animation: { animateRotate: true, duration: 1500 }
                }
            });

            let rotation = 0;
            const spin = () => {
                rotation += 0.005;
                occChart.options.rotation = rotation;
                occChart.update('none');
                requestAnimationFrame(spin);
            };
            spin();
        });
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-in-out;
        }

        @keyframes pulse-slow {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.85;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s infinite ease-in-out;
        }

        canvas {
            transition: all 0.3s ease-in-out;
        }

        canvas:hover {
            filter: brightness(1.05) drop-shadow(0 2px 6px rgba(99, 102, 241, 0.25));
        }
    </style>
@endpush