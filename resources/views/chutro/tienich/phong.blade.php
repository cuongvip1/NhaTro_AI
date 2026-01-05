@extends('layouts.chu-tro')

@section('title', 'Tiện ích của phòng')

@section('content')
<div class="max-w-5xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
        🏠 Tiện ích của phòng {{ $phong->so_phong }} ({{ $phong->dayTro->ten_day_tro }})
    </h2>

    {{-- ✅ Danh sách tiện ích --}}
    <form action="{{ route('chu-tro.tien-ich.gan', $phong->id) }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($tatCaTienIch as $ti)
                <label class="flex items-center gap-2 border border-gray-200 rounded-lg px-4 py-3 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="tien_ich_ids[]" value="{{ $ti->id }}"
                        @if ($phong->tienIch->contains($ti->id)) checked @endif
                        class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-gray-700 font-medium">{{ $ti->ten }}</span>
                </label>
            @endforeach
        </div>

        <div class="mt-6 flex justify-between items-center">
            <a href="{{ route('chu-tro.phong.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                ⬅️ Quay lại danh sách phòng
            </a>

            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                💾 Lưu thay đổi
            </button>
        </div>
    </form>
</div>
@endsection
