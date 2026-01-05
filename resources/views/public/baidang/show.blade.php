@extends('layouts.public')
@section('title', $b->tieu_de)

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold text-indigo-700 mb-6">{{ $b->tieu_de }}</h1>

        {{-- Ảnh --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            @foreach ($b->anh as $a)
                <img src="{{ asset('storage/' . $a) }}" class="rounded-xl shadow-md object-cover w-full h-64">
            @endforeach
        </div>

        {{-- Thông tin chi tiết --}}
        <div class="bg-white shadow-lg rounded-2xl p-6 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-fuchsia-600 text-2xl font-semibold">{{ $b->gia_hien_thi }}</span>
                <span class="text-gray-500"><i class="ri-map-pin-2-line"></i> {{ $b->dia_chi }}</span>
            </div>

            <p class="text-gray-700 leading-relaxed">{{ $b->mo_ta }}</p>

            <div class="border-t pt-4">
                <h3 class="text-indigo-700 font-semibold mb-2 flex items-center gap-2">
                    <i class="ri-lightbulb-flash-line"></i> Tiện ích
                </h3>
                @if ($b->tien_ich)
                    <div class="flex flex-wrap gap-2">
                        @foreach (explode(',', $b->tien_ich) as $t)
                            <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm">
                                <i class="ri-check-line mr-1"></i> {{ ucfirst(trim($t)) }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">Chưa có thông tin tiện ích.</p>
                @endif
            </div>
            {{-- 💰 Dịch vụ --}}
<div class="border-t pt-4">
    <h3 class="text-indigo-700 font-semibold mb-2 flex items-center gap-2">
        <i class="ri-bill-line"></i> Dịch vụ
    </h3>

    @if (!empty($b->dich_vu) && count($b->dich_vu))
        <ul class="space-y-2">
            @foreach ($b->dich_vu as $dv)
                <li class="flex justify-between items-center text-gray-700">
                    <span>
                        <i class="ri-checkbox-circle-line text-green-500 mr-1"></i>
                        {{ $dv->ten }}
                        <span class="text-sm text-gray-500">({{ $dv->don_vi }})</span>
                    </span>
                    <span class="font-semibold text-indigo-600">
                        {{ number_format($dv->gia, 0, ',', '.') }} đ
                    </span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500 italic">Chưa có thông tin dịch vụ.</p>
    @endif
</div>


            <div class="pt-4">
                <a href="tel:0909123456"
                    class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="ri-phone-line"></i> Liên hệ chủ trọ
                </a>
            </div>
        </div>
    </div>
@endsection