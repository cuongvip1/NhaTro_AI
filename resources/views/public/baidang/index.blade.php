@extends('layouts.public')
@section('title', 'Danh sách phòng trọ')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold text-indigo-700 mb-8 text-center">
            🏡 Danh sách phòng trọ hiện có
        </h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($bai_dang as $b)
                <a href="{{ route('public.bai-dang.show', $b->id) }}"
                    class="group block bg-white rounded-2xl shadow hover:shadow-xl transition-all overflow-hidden">
                    <div class="relative">
                        <img src="{{ asset('storage/' . $b->anh) }}" alt="Ảnh phòng"
                            class="h-52 w-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span
                            class="absolute top-2 right-2 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                            Còn phòng
                        </span>
                    </div>
                    <div class="p-4 space-y-2">
                        <h2 class="text-lg font-semibold text-indigo-700 line-clamp-1">{{ $b->tieu_de }}</h2>
                        <p class="text-gray-600 text-sm line-clamp-2">{{ $b->mo_ta }}</p>

                        <div class="flex justify-between items-center mt-3">
                            <span class="text-lg font-bold text-fuchsia-600">{{ $b->gia_hien_thi }}</span>
                            <span class="text-sm text-gray-500">
                                <i class="ri-map-pin-line mr-1"></i>{{ $b->ten_day_tro }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $bai_dang->links() }}
        </div>
    </div>
@endsection