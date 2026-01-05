@extends('layouts.chu-tro')

@section('title', 'Chi tiết phòng')

@section('content')
    <div class="max-w-5xl mx-auto py-10 px-6">

        {{-- Nút quay lại --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('chu-tro.phong.index') }}"
                class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                <i class="ri-arrow-left-line mr-1"></i> Quay lại danh sách phòng
            </a>

            <a href="{{ route('chu-tro.phong.edit', $phong->id) }}"
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                <i class="ri-edit-2-line mr-1"></i> Chỉnh sửa phòng
            </a>
        </div>

        {{-- Thông tin phòng --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl ring-1 ring-gray-900/5 p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <i class="ri-home-2-line text-indigo-500 text-2xl"></i>
                Phòng {{ $phong->so_phong }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm text-gray-700 dark:text-gray-300">
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Dãy trọ:</p>
                    <p>{{ $phong->ten_day_tro }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Địa chỉ:</p>
                    <p>{{ $phong->dia_chi }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Giá thuê:</p>
                    <p>{{ number_format($phong->gia, 0, ',', '.') }} đ / tháng</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Trạng thái:</p>
                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @if($phong->trang_thai === 'trong') bg-green-100 text-green-700
                                        @elseif($phong->trang_thai === 'da_thue') bg-blue-100 text-blue-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ ucfirst($phong->trang_thai) }}
                    </span>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Sức chứa:</p>
                    <p>{{ $phong->suc_chua }} người</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Diện tích:</p>
                    <p>{{ $phong->dien_tich }} m²</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Tầng:</p>
                    <p>{{ $phong->tang }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Ngày tạo:</p>
                    <p>{{ \Carbon\Carbon::parse($phong->ngay_tao)->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Ngày cập nhật:</p>
                    <p>{{ \Carbon\Carbon::parse($phong->ngay_cap_nhat)->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            {{-- Tiện ích --}}
            <div class="mt-8">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-1">
                    <i class="ri-star-smile-line text-yellow-500 text-xl"></i> Tiện ích phòng
                </h3>

                @if($tienIch->isEmpty())
                    <p class="text-gray-500 text-sm">Chưa có tiện ích nào được thêm.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tienIch as $ti)
                            <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-sm font-medium">
                                <i class="ri-check-line mr-1"></i>{{ $ti }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection