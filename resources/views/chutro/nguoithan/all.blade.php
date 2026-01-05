@extends('layouts.chu-tro')

@section('title', '👨‍👩‍👧‍👦 Danh sách người thân')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-10">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('chu-tro.dashboard') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-full shadow-sm hover:bg-gray-50 hover:text-indigo-600 transition">
                    <i class="ri-arrow-go-back-line"></i> Quay lại 
                </a>

                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"
                            class="w-7 h-7 text-indigo-600">
                            <path d="M13 14s-1 0-1-1 1-4-4-4-4 3-4 4-1 1-1 1h10z" />
                            <path fill-rule="evenodd" d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        </svg>
                        Danh sách người thân
                    </h2>
                    <p class="text-gray-500 mt-1 text-sm">Theo dõi và quản lý người thân của khách thuê trong hệ thống.</p>
                </div>
            </div>
            <div
                class="flex flex-wrap gap-3 mt-4 sm:mt-0 bg-white border border-gray-200 rounded-full shadow-sm px-4 py-2.5 items-center transition hover:shadow-md">
                @php
                    $dsKhach = collect($nguoiThan)
                        ->pluck('ten_khach_thue', 'khach_thue_id')
                        ->unique();
                @endphp

                <div class="relative">
                    <select id="filterKhachThue"
                        class="appearance-none bg-transparent pl-3 pr-8 py-1.5 text-sm text-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                        <option value="">🧍‍♂️ Tất cả khách thuê</option>
                        @foreach ($dsKhach as $id => $ten)
                            <option value="{{ $id }}">{{ $ten }}</option>
                        @endforeach
                    </select>

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="absolute right-1.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9.75L12 13.5l3.75-3.75" />
                    </svg>
                </div>

                <div class="relative flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="absolute left-3 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                    </svg>
                    <input type="text" id="searchInput"
                        class="pl-9 pr-4 py-1.5 text-sm border-0 focus:ring-0 bg-transparent outline-none text-gray-700 placeholder-gray-400"
                        placeholder="Tìm kiếm người thân...">
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 rounded-lg p-3 mb-5 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2l4-4m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 rounded-lg p-3 mb-5 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div id="nguoiThanCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($nguoiThan as $nt)
                <div data-khach="{{ $nt['khach_thue_id'] ?? '' }}"
                    class="bg-white rounded-2xl shadow-sm hover:shadow-md transition duration-300 p-6 flex flex-col justify-between border border-gray-100">
                    <div>
                        <div class="flex items-center mb-4">
                            <div
                                class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-blue-500 text-white font-semibold text-lg">
                                {{ strtoupper(mb_substr($nt['ho_ten'], 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <h5 class="font-bold text-gray-800">{{ $nt['ho_ten'] }}</h5>
                                <p class="text-gray-500 text-sm">{{ $nt['moi_quan_he'] ?? 'Không rõ' }}</p>
                            </div>
                        </div>

                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><i class="bi bi-telephone-fill text-green-500 me-2"></i>SĐT: {{ $nt['so_dien_thoai'] ?? '-' }}</li>
                            <li><i class="bi bi-house-door-fill text-blue-500 me-2"></i>Khách thuê:
                                <span class="font-semibold text-gray-800">{{ $nt['ten_khach_thue'] ?? ('#' . $nt['khach_thue_id']) }}</span>
                            </li>
                        </ul>
                    </div>

                    @if (!empty($nt['khach_thue_id']))
                        <form action="{{ route('chu-tro.nguoi-than.destroy', [$nt['id'], $nt['khach_thue_id']]) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn xóa người thân này?')" class="mt-5">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-full py-2 text-sm font-medium text-red-600 border border-red-200 rounded-full hover:bg-red-50 transition">
                                <i class="bi bi-trash me-1"></i> Xóa người thân
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-10">
                    <i class="bi bi-emoji-frown text-2xl"></i>
                    <p class="mt-2">Không có người thân nào trong hệ thống.</p>
                </div>
            @endforelse
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const filterKhach = document.getElementById('filterKhachThue');
            const cards = document.querySelectorAll('#nguoiThanCards > div');

            function filterCards() {
                const text = searchInput.value.toLowerCase().trim();
                const selectedKhach = filterKhach.value;

                cards.forEach(card => {
                    const content = card.textContent.toLowerCase();
                    const khach = card.dataset.khach;
                    const matchText = content.includes(text);
                    const matchKhach = selectedKhach === '' || khach === selectedKhach;
                    card.style.display = (matchText && matchKhach) ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterCards);
            filterKhach.addEventListener('change', filterCards);
        });
    </script>
@endsection
