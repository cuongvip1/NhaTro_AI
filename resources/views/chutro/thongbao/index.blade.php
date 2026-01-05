@extends('layouts.chu-tro')

@section('title', 'Thông báo của bạn')

@section('content')
    <div class="p-8 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto">

            {{-- Tiêu đề --}}
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="ri-notification-3-line text-indigo-500 text-3xl"></i>
                    Thông báo của bạn
                </h1>
                <div class="flex items-center gap-3">
                    @if (true)
                        <button id="mark-all-read" onclick="markAllAsRead()"
                            class="px-4 py-2 text-sm bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl shadow transition">
                            Đánh dấu tất cả đã xem
                        </button>
                    @endif

                    @if ($thong_bao->where('da_xem', 1)->count() > 0)
                        <button id="delete-read"
                            class="px-4 py-2 text-sm bg-red-500 hover:bg-red-600 text-white rounded-xl shadow transition">
                            Xóa tất cả đã đọc
                        </button>
                    @endif

                    {{-- Nút quay lại --}}
                    <a href="{{ route('chu-tro.dashboard') }}"
                        class="text-sm text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                        <i class="ri-arrow-left-line"></i> Quay lại
                    </a>
                </div>
            </div>

            {{-- Danh sách thông báo --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                @if ($thong_bao->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        <i class="ri-inbox-line text-4xl mb-2 text-gray-400"></i>
                        <p>Hiện chưa có thông báo nào.</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @forelse($thong_bao ?? [] as $tb)
                            <div @click.stop="open = false" onclick="markAsRead({{ $tb->id }}, '{{ $tb->lien_ket ?? '#' }}')"
                                class="block p-3 text-sm cursor-pointer transition relative
                                                                                                                                                                                                                                                                                                                                         {{ $tb->da_xem ? 'bg-white opacity-60' : 'bg-indigo-50 font-semibold hover:bg-indigo-100' }}">
                                <p class="text-gray-800">{{ $tb->noi_dung }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($tb->ngay_tao)->diffForHumans() }}
                                </p>

                                @if(!$tb->da_xem)
                                    <span
                                        class="absolute top-2 right-3 bg-indigo-500 text-white text-[10px] px-2 py-0.5 rounded-full">Mới</span>
                                @else
                                    <span
                                        class="absolute top-2 right-3 bg-gray-300 text-gray-700 text-[10px] px-2 py-0.5 rounded-full">Đã
                                        xem</span>
                                @endif
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500 text-sm">
                                Không có thông báo nào
                            </div>
                        @endforelse

                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Script --}}
    @push('scripts')
        <script>
            function markAsRead(id, link) {
                fetch(`/chu-tro/thong-bao/${id}/mark-as-read`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const badge = document.querySelector(
                                '.absolute.-top-1.-right-1.bg-rose-500.text-white.text-xs.w-4.h-4.flex.items-center.justify-center.rounded-full:last-of-type'
                            );
                            if (badge) {
                                let current = parseInt(badge.textContent || "0");
                                if (current > 1) badge.textContent = current - 1;
                                else badge.remove();
                            }

                            setTimeout(() => {
                                window.location.href = link;
                            }, 150);
                        }
                    })
                    .catch(() => {
                        setTimeout(() => {
                            window.location.href = link;
                        }, 150);
                    });
            }
        </script>
        <script>
            function markAllAsRead() {
                fetch(`/chu-tro/thong-bao/mark-all-read`, {
                    method: "POST",
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
                                title: 'Đã đánh dấu tất cả!',
                                text: 'Tất cả thông báo đã được đánh dấu là đã xem.',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            document.querySelectorAll('.block.p-3').forEach(div => {
                                div.classList.remove('bg-indigo-50', 'font-semibold', 'hover:bg-indigo-100');
                                div.classList.add('bg-white', 'opacity-60');

                                const badge = div.querySelector('.absolute.top-2.right-3');
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
        </script>

        <script>
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

                    fetch(`{{ route('chu-tro.thong-bao.xoa-da-doc') }}`, {
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

                                // Xóa thông báo đã đọc khỏi giao diện
                                document.querySelectorAll('.bg-white.opacity-60').forEach(el => el.remove());
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

            // Gắn event listener vào nút
            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.getElementById('delete-read');
                if (btn) btn.addEventListener('click', xoaThongBaoDaDoc);
            });
        </script>

    @endpush
@endsection