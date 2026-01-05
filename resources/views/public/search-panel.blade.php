@extends('layouts.app')

@section('title', 'Danh sách phòng trọ - Hệ Thống Trọ')

@section('content')
<!-- Search Panel moved from home.blade.php -->
<section class="relative py-16 bg-gray-50 dark:bg-gray-900 reveal" x-data="searchForm()">

  <div class="max-w-5xl mx-auto px-6">
    <div class="text-center mb-10">
  <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white">Danh sách phòng trọ</h2>
      <p class="text-gray-500 dark:text-gray-300 mt-2">Sử dụng bộ lọc thông minh để tìm phòng trọ phù hợp nhất</p>
    </div>

    <form action="{{ route('listing') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg p-8 space-y-6">
      {{-- Khu vực --}}
      <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
          <i class="ri-map-pin-2-line mr-1 text-indigo-600"></i> Khu vực mong muốn
        </label>
        <div class="relative">
          <input type="text" name="area" placeholder="Nhập quận, huyện, đường..." x-model="filters.area"
            class="w-full px-5 py-3 border border-gray-300 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 text-gray-800 dark:text-gray-100 dark:bg-gray-900">
          <i class="ri-search-line absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
        </div>

        {{-- Khu vực phổ biến --}}
        <div class="mt-5">
          <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2 flex items-center">
            <i class="ri-map-pin-user-line mr-1 text-indigo-500"></i> Khu vực phổ biến
          </p>
          <div class="flex flex-wrap gap-2">
            @foreach (['Quận 1', 'Quận 3', 'Quận 7', 'Thủ Đức', 'Bình Thạnh', 'Quận 2'] as $kv)
              <button type="button"
                @click="toggleArea('{{ $kv }}')"
                :class="filters.area === '{{ $kv }}'
                  ? 'bg-indigo-600 text-white border-indigo-600 shadow-md'
                  : 'bg-gray-100 dark:bg-gray-900 hover:bg-indigo-50 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-indigo-400'"
                class="px-4 py-1.5 rounded-full text-sm border shadow-sm hover:shadow transition">
                {{ $kv }}
              </button>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Khoảng giá --}}
      <div>
        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2 flex items-center">
          <i class="ri-money-dollar-circle-line mr-1 text-indigo-500"></i> Khoảng giá mong muốn
        </p>
        <div class="flex items-center gap-3">
          <input type="number" x-model="filters.min" name="min_price" placeholder="Từ (₫)"
            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 dark:bg-gray-900 dark:text-gray-100">
          <span class="text-gray-500 font-medium">—</span>
          <input type="number" x-model="filters.max" name="max_price" placeholder="Đến (₫)"
            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 dark:bg-gray-900 dark:text-gray-100">
        </div>

        {{-- Gợi ý nhanh --}}
        <div class="flex flex-wrap gap-2 mt-3">
          @foreach (['< 2 triệu' => [0, 2000000], '2 - 3 triệu' => [2000000, 3000000], '3 - 5 triệu' => [3000000, 5000000], '> 5 triệu' => [5000000, 0]] as $label => $range)
            <button type="button"
              @click="setPrice({{ $range[0] }}, {{ $range[1] }})"
              :class="filters.min == {{ $range[0] }} && filters.max == {{ $range[1] }}
                ? 'bg-indigo-600 text-white border-indigo-600 shadow-md'
                : 'bg-gray-100 dark:bg-gray-900 hover:bg-indigo-50 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-indigo-400'"
              class="px-4 py-1.5 rounded-full text-sm border shadow-sm hover:shadow transition">
              {{ $label }}
            </button>
          @endforeach
        </div>
      </div>

      {{-- Sắp xếp + Submit --}}
      <div class="flex items-end gap-4">
        <div class="flex-1">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
            <i class="ri-sort-desc text-indigo-600 mr-1"></i> Sắp xếp theo
          </label>
          <select name="sort" x-model="filters.sort"
            class="w-full px-5 py-3 border border-gray-300 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-100 dark:bg-gray-900">
            <option value="asc">Giá thấp → cao</option>
            <option value="desc">Giá cao → thấp</option>
            <option value="newest">Mới nhất</option>
            <option value="area">Diện tích lớn → nhỏ</option>
          </select>
        </div>

        <button type="submit"
          class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-2xl shadow-md hover:shadow-lg transition transform hover:scale-[1.03]">
          <i class="ri-search-eye-line mr-2"></i> Danh sách phòng trọ
        </button>
      </div>

      {{-- Bộ lọc nhanh removed per request --}}
    </form>
  </div>
</section>

{{-- Alpine helper (moved with the panel) --}}
<script>
  function searchForm() {
    return {
      filters: { area:'', min:'', max:'', sort:'asc' },
      toggleArea(area){ this.filters.area = this.filters.area === area ? '' : area },
      setPrice(min,max){ this.filters.min=min; this.filters.max=max },
      // quick filter helper removed with UI
    }
  }
</script>

@endsection
