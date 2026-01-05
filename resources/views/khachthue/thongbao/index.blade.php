@extends('layouts.tenant-layout')

@section('title', 'Thông báo')
@section('page_title', 'Thông báo của bạn')

@section('tenant_content')
    @php
        $collection = collect($thongBao ?? []);
        $soChuaDoc = $collection->where('da_xem', 0)->count();
        $soDaDoc   = $collection->where('da_xem', 1)->count();
    @endphp

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            <i class="ri-notification-3-line text-indigo-500 text-2xl"></i>
            Trung tâm thông báo
        </h2>

        <div class="flex items-center gap-3">
            {{-- Đánh dấu tất cả đã xem --}}
            @if ($soChuaDoc > 0)
                <button id="mark-all-read"
                        onclick="markAllAsRead()"
                        class="px-3 py-1.5 text-xs md:text-sm bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl shadow transition">
                    Đánh dấu tất cả đã xem
                </button>
            @endif

            {{-- Xóa tất cả đã đọc --}}
            @if ($soDaDoc > 0)
                <button id="delete-read"
                        class="px-3 py-1.5 text-xs md:text-sm bg-red-500 hover:bg-red-600 text-white rounded-xl shadow transition">
                    Xóa tất cả đã đọc
                </button>
            @endif
        </div>
    </div>

    @if(empty($thongBao) || count($thongBao) === 0)
        <div class="flex flex-col items-center justify-center h-56 text-gray-500 dark:text-gray-400">
            <i class="ri-notification-off-line text-4xl mb-3"></i>
            <p>Bạn chưa có thông báo nào.</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($thongBao as $tb)
                    <li
                        onclick="markAsRead({{ $tb['id'] }}, '{{ $tb['lien_ket'] ?? '#' }}')"
                        class="block p-4 text-sm cursor-pointer transition relative
                            {{ ($tb['da_xem'] ?? 0) == 0
                                ? 'bg-indigo-50/80 dark:bg-gray-700/60 font-semibold hover:bg-indigo-100'
                                : 'bg-white dark:bg-gray-800 opacity-60' }}"
                    >
                        <p class="text-gray-800 dark:text-gray-100">
                            {{ $tb['noi_dung'] ?? '(Không có nội dung)' }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            🕒 {{ \Carbon\Carbon::parse($tb['ngay_tao'])->diffForHumans() }}
                        </p>

                        @if(($tb['da_xem'] ?? 0) == 0)
                            <span
                                class="absolute top-3 right-4 bg-indigo-500 text-white text-[10px] px-2 py-0.5 rounded-full">
                                Mới
                            </span>
                        @else
                            <span
                                class="absolute top-3 right-4 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-100 text-[10px] px-2 py-0.5 rounded-full">
                                Đã xem
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // 🔔 Cập nhật số trên chuông
        function setBellCount(newCount) {
            const badge = document.getElementById('tenant-bell-badge');
            const icon  = document.getElementById('tenant-bell-icon');

            if (newCount <= 0) {
                // Không còn thông báo chưa đọc → xóa badge + tắt rung
                if (badge) badge.remove();
                if (icon) icon.classList.remove('tenant-bell-ring');
                return;
            }

            let b = badge;
            if (!b) {
                // Tạo badge mới khi trước đó không có
                const btn = document.getElementById('tenant-bell-btn');
                if (!btn) return;

                b = document.createElement('span');
                b.id = "tenant-bell-badge";
                b.className = "absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center";
                btn.appendChild(b);
            }

            b.dataset.count = newCount;
            b.textContent = newCount > 99 ? "99+" : newCount;

            // Thêm hiệu ứng rung
            if (icon && !icon.classList.contains('tenant-bell-ring')) {
                icon.classList.add('tenant-bell-ring');
            }
        }

        function decreaseBellCount(delta = 1) {
            const badge = document.getElementById('tenant-bell-badge');
            if (!badge) return;

            let current = parseInt(badge.dataset.count || badge.textContent || "0", 10);
            setBellCount(current - delta);
        }

        // Đánh dấu 1 thông báo đã xem rồi chuyển sang link
        function markAsRead(id, link) {
            fetch(`/khach-thue/thong-bao/${id}/mark-as-read`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // 🔻 Giảm số trên chuông đi 1
                        decreaseBellCount(1);
                    }

                    setTimeout(() => {
                        window.location.href = link;
                    }, 150);
                })
                .catch(() => {
                    setTimeout(() => {
                        window.location.href = link;
                    }, 150);
                });
        }

        // Đánh dấu tất cả đã xem
        function markAllAsRead() {
            fetch(`/khach-thue/thong-bao/mark-all-read`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // 🔻 Chuông về 0
                        setBellCount(0);

                        Swal.fire({
                            icon: 'success',
                            title: 'Đã đánh dấu tất cả!',
                            text: data.message || 'Tất cả thông báo đã được đánh dấu là đã xem.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        document.querySelectorAll('li.block.p-4').forEach(li => {
                            li.classList.remove('bg-indigo-50/80', 'font-semibold', 'hover:bg-indigo-100');
                            li.classList.add('bg-white', 'opacity-60');

                            const badge = li.querySelector('span.absolute.top-3.right-4');
                            if (badge) {
                                badge.classList.remove('bg-indigo-500', 'text-white');
                                badge.classList.add('bg-gray-300', 'text-gray-700');
                                badge.textContent = 'Đã xem';
                            }
                        });

                        const btn = document.getElementById('mark-all-read');
                        if (btn) btn.style.display = 'none';
                    }
                })
                .catch(() => {
                    Swal.fire('Lỗi!', 'Không thể đánh dấu tất cả đã xem.', 'error');
                });
        }

        // Xóa tất cả thông báo đã đọc
        function xoaThongBaoDaDoc() {
            Swal.fire({
                icon: 'warning',
                title: 'Xóa tất cả thông báo đã đọc?',
                text: 'Hành động này sẽ xóa vĩnh viễn các thông báo đã xem.',
                showCancelButton: true,
                confirmButtonText: 'Xóa luôn',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#ef4444'
            }).then(result => {
                if (!result.isConfirmed) return;

                fetch(`{{ route('khach-thue.thong-bao.xoa-da-doc') }}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Đã xóa 🎉',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Xóa khỏi UI: các item đã đọc (bg-white.opacity-60)
                            document.querySelectorAll('li.bg-white.opacity-60').forEach(el => el.remove());
                        } else {
                            Swal.fire('Lỗi!', data.message || 'Không thể xóa thông báo.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Lỗi!', 'Không thể kết nối máy chủ.', 'error');
                        console.error(err);
                    });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('delete-read');
            if (btn) btn.addEventListener('click', xoaThongBaoDaDoc);
        });
    </script>
@endpush
