@extends('layouts.app')
@section('title', 'Danh sách phòng trọ')

@section('content')

@php
  $recommendations = $recommendations ?? [];
  $recommend_error = $recommend_error ?? null;
  $recommend_payload = $recommend_payload ?? null;
  $activeMode = $activeMode ?? 'search';
  $serviceOptions = collect($services ?? [])->map(function ($service) {
    $item = is_array($service) ? $service : (array) $service;
    $name = $item['ten_hien_thi'] ?? $item['ten_dich_vu'] ?? $item['ten'] ?? null;
    $price = $item['gia_hien_thi'] ?? $item['gia'] ?? $item['don_gia'] ?? null;
    return [
      'id' => $item['id'] ?? null,
      'name' => $name,
      'price' => $price,
      'unit' => $item['don_vi'] ?? null,
      'code' => $item['ma'] ?? null,
    ];
  })->filter(function ($service) {
    return !empty($service['id']) && !empty($service['name']);
  })->values()->toArray();
  $selectedServiceIds = array_values(array_filter(array_map(function ($value) {
    $value = trim((string) $value);
    return $value === '' ? null : $value;
  }, explode(',', old('dich_vu_id', request('dich_vu_id', ''))))));
@endphp

  {{-- 🔍 FORM DANH SÁCH PHÒNG TRỌ --}}
    <section class="relative py-12 bg-gray-50 dark:bg-gray-900" x-data="{ activeTab: '{{ $activeMode === 'ai' ? 'ai' : 'search' }}' }">
  <div class="max-w-6xl mx-auto px-6">
    <div class="text-center mb-10">
  <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white">Danh sách phòng trọ</h2>
      <p class="text-gray-500 dark:text-gray-300 mt-2">Chọn tab bên dưới để tìm hoặc đề xuất phòng nhanh chóng</p>
    </div>

    <div class="flex flex-wrap gap-4 justify-center mb-8">
      <button type="button" @click="activeTab = 'search'"
        :class="activeTab === 'search' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200'"
        class="px-6 py-2.5 rounded-full font-semibold border border-indigo-200 transition">
        Tìm kiếm phòng trọ
      </button>
      <button type="button" @click="activeTab = 'ai'"
        :class="activeTab === 'ai' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200'"
        class="px-6 py-2.5 rounded-full font-semibold border border-indigo-200 transition">
        Đề xuất phòng trọ
      </button>
    </div>

    <div x-show="activeTab === 'search'" x-cloak>
      <div x-data="searchForm()">
    <form action="{{ route('listing') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg p-8 space-y-6">
      <input type="hidden" name="mode" value="search">

      {{-- KHU VỰC --}}
      <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
          <i class="ri-map-pin-2-line mr-1 text-indigo-600"></i> Khu vực mong muốn
        </label>
        <div class="relative">
          <input type="text" name="dia_chi" placeholder="Nhập quận, huyện, đường..." x-model="filters.area"
            value="{{ request('dia_chi') }}" list="listing-region-list"
            class="w-full px-5 py-3 border border-gray-300 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 text-gray-800 dark:text-gray-100 dark:bg-gray-900">
          <datalist id="listing-region-list">
            @foreach(($regions ?? []) as $region)
              @php
                $name = is_array($region) ? ($region['ten_dia_chi'] ?? '') : ($region->ten_dia_chi ?? '');
              @endphp
              @if(!empty($name))
                <option value="{{ $name }}"></option>
              @endif
            @endforeach
          </datalist>
          <i class="ri-search-line absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
        </div>
      </div>

      {{-- KHOẢNG GIÁ --}}
      <div>
        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2 flex items-center">
          <i class="ri-money-dollar-circle-line mr-1 text-indigo-500"></i> Khoảng giá mong muốn
        </p>
        <div class="flex items-center gap-3">
          <input type="number" x-model="filters.min" name="min" placeholder="Từ (₫)" value="{{ request('min') }}"
            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 dark:bg-gray-900 dark:text-gray-100">
          <span class="text-gray-500 font-medium">—</span>
          <input type="number" x-model="filters.max" name="max" placeholder="Đến (₫)" value="{{ request('max') }}"
            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 dark:bg-gray-900 dark:text-gray-100">
        </div>

        {{-- GỢI Ý NHANH --}}
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

      {{-- Diện tích mong muốn removed per request --}}

      {{-- SẮP XẾP + NÚT TÌM --}}
      <div class="flex items-end gap-4">
        <div class="flex-1">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
            <i class="ri-sort-desc text-indigo-600 mr-1"></i> Sắp xếp theo
          </label>
          <select name="sort" x-model="filters.sort"
            class="w-full px-5 py-3 border border-gray-300 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-100 dark:bg-gray-900">
            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Giá thấp → cao</option>
            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Giá cao → thấp</option>
            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
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
    </div>

    <div x-show="activeTab === 'ai'" x-cloak>
      <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg p-6">
        <div class="flex flex-col md:flex-row md:items-start gap-4">
          <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
              <i class="ri-robot-line text-indigo-500 text-2xl"></i>
              Đề xuất phòng trọ
            </h3>
          </div>

          <form action="{{ route('listing.recommend') }}" method="POST" class="w-full md:w-auto md:flex md:items-start gap-4">
            @csrf
            <input type="hidden" name="mode" value="ai">
            <div class="flex-1">
              <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Ngân sách tối đa (₫)</label>
              <input type="number" name="budget" min="0" required
                value="{{ old('budget', request('budget', request('max'))) }}"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-100 dark:bg-gray-900">
              @error('budget')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
              @enderror
            </div>
            <div class="flex-1">
              <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Diện tích mong muốn (m²)</label>
              <input type="number" name="area" min="0" step="0.1" required
                value="{{ old('area', request('area')) }}"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-100 dark:bg-gray-900">
              @error('area')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
              @enderror
            </div>
            <div class="flex-1" x-data='{
                showServiceModal: false,
                options: {!! json_encode($serviceOptions) !!},
                selected: {!! json_encode($selectedServiceIds) !!},
                get selectedDetails() {
                  return this.options.filter(opt => this.selected.includes(String(opt.id)));
                },
                toggleService(id) {
                  id = String(id);
                  if (this.selected.includes(id)) {
                    this.selected = this.selected.filter(item => item !== id);
                  } else {
                    this.selected.push(id);
                  }
                },
                isSelected(id) {
                  return this.selected.includes(String(id));
                },
                openModal() {
                  this.showServiceModal = true;
                  document.documentElement.classList.add("overflow-hidden");
                },
                closeModal() {
                  this.showServiceModal = false;
                  document.documentElement.classList.remove("overflow-hidden");
                }
              }'>
              <label class="block text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Dịch vụ mong muốn</label>
              <input type="hidden" name="dich_vu_id" :value="selected.join(',')">
              <button type="button" @click="openModal()"
                class="w-full flex items-center justify-between px-4 py-2.5 border border-dashed border-indigo-300 dark:border-indigo-500/60 rounded-2xl text-left text-gray-800 dark:text-gray-100 dark:bg-gray-900 hover:border-indigo-500 transition">
                <span>
                  <span class="font-semibold block text-sm text-indigo-600">Chọn dịch vụ đi kèm</span>
                  <span class="text-xs text-gray-500" x-show="selected.length === 0">Nhấn để mở danh sách dịch vụ</span>
                  <span class="text-xs text-gray-500" x-show="selected.length > 0" x-text="selected.length + ' dịch vụ đã chọn'"></span>
                </span>
                <i class="ri-arrow-down-s-line text-2xl text-indigo-500"></i>
              </button>

              <div class="flex flex-wrap gap-2 mt-3" x-show="selectedDetails.length" x-cloak>
                <template x-for="svc in selectedDetails" :key="svc.id">
                  <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-sm">
                    <i class="ri-magic-line"></i>
                    <span x-text="svc.name"></span>
                    <button type="button" class="text-indigo-500 hover:text-indigo-700" @click="toggleService(svc.id)">
                      <i class="ri-close-circle-line"></i>
                    </button>
                  </span>
                </template>
              </div>

              <p class="text-sm text-gray-500 mt-2" x-show="!selectedDetails.length && options.length" x-cloak>Chưa chọn dịch vụ nào.</p>
              <p class="text-sm text-amber-600 mt-2" x-show="!options.length" x-cloak>Hiện chưa có dữ liệu dịch vụ để lựa chọn.</p>

              @error('dich_vu_id')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
              @enderror

              <!-- Modal chọn dịch vụ -->
              <div x-show="showServiceModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/40" @click="closeModal()"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl mx-4 p-6">
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Danh sách dịch vụ</p>
                      <h4 class="text-xl font-bold text-gray-900 dark:text-white">Chọn dịch vụ phù hợp</h4>
                    </div>
                    <button type="button" class="text-gray-500 hover:text-gray-800 dark:hover:text-white text-2xl" @click="closeModal()">
                      &times;
                    </button>
                  </div>

                  <div class="max-h-[60vh] overflow-y-auto pr-2 space-y-2">
                    <template x-if="!options.length">
                      <p class="text-gray-500 text-sm">Không có dịch vụ nào để hiển thị.</p>
                    </template>

                    <template x-for="svc in options" :key="svc.id">
                      <label class="flex items-start gap-3 p-3 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-indigo-400 cursor-pointer transition">
                        <input type="checkbox" class="mt-1 form-checkbox h-4 w-4 text-indigo-600" :value="svc.id" :checked="isSelected(svc.id)" @change="toggleService(svc.id)">
                        <div>
                          <p class="font-semibold text-gray-800 dark:text-gray-100" x-text="svc.name"></p>
                        </div>
                      </label>
                    </template>
                  </div>

                  <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-2xl border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-200" @click="selected = []; closeModal();">
                      Bỏ chọn
                    </button>
                    <button type="button" class="px-5 py-2 rounded-2xl bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-semibold" @click="closeModal()">
                      Xong
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <input type="hidden" name="min" value="{{ request('min') }}">
            <input type="hidden" name="max" value="{{ request('max') }}">
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            @foreach((array) request('filters', []) as $filter)
              <input type="hidden" name="filters[]" value="{{ $filter }}">
            @endforeach

            <button type="submit"
              class="px-6 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-semibold rounded-2xl shadow-md hover:shadow-lg transition">
              <i class="ri-robot-line mr-2"></i>Đề xuất phòng
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

    {{-- 💜 DANH SÁCH PHÒNG TRỌ --}}
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-6">
        @if($recommend_error)
          <div class="mb-8 p-4 border border-red-200 bg-red-50 text-red-600 rounded-2xl">
            {{ $recommend_error }}
          </div>
        @endif

        @if(!empty($recommendations))
          <div class="mb-10 bg-gradient-to-br from-purple-50 via-white to-indigo-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-3xl p-6 shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
              <div>
                <p class="text-sm uppercase tracking-wide text-indigo-500 font-semibold">Gợi ý dành riêng cho bạn</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Top 3 phòng phù hợp nhất</h3>
              </div>
              @if(!empty($recommend_payload))
                <div class="text-sm text-gray-600 dark:text-gray-300">
                  <p>Ngân sách: <span class="font-semibold text-indigo-600">{{ number_format($recommend_payload['budget'] ?? 0, 0, ',', '.') }}₫</span></p>
                  <p>Diện tích: <span class="font-semibold text-indigo-600">{{ $recommend_payload['area'] ?? 0 }} m²</span></p>
                  @if(!empty($recommend_payload['dich_vu']))
                    @php
                      $payloadServices = is_array($recommend_payload['dich_vu']) ? array_filter($recommend_payload['dich_vu']) : [];
                    @endphp
                    @if(!empty($payloadServices))
                      <p>Dịch vụ: <span class="font-semibold text-indigo-600">{{ implode(', ', $payloadServices) }}</span></p>
                    @endif
                  @endif
                </div>
              @endif
            </div>

            <div class="grid gap-6 md:grid-cols-3">
              @foreach($recommendations as $rec)
                @php
                  $item = is_array($rec) ? $rec : (array) $rec;
                  $detailUrl = isset($item['id']) ? url('/bai-dang/' . $item['id']) : '#';
                  $thumb = $item['anh_dai_dien'] ?? asset('upload/room1.jpg');
                  //$tags = array_filter(array_slice(explode(',', $item['tien_ich'] ?? ''), 0, 3));
                  $tienIch = $item['tien_ich'] ?? [];

if (is_array($tienIch)) {
    $tags = array_filter(array_slice($tienIch, 0, 3));
} else {
    $tags = array_filter(array_slice(explode(',', (string) $tienIch), 0, 3));
}

                  $priceDisplay = $item['gia_hien_thi']
                    ?? ($item['gia_niem_yet'] ?? null ? number_format($item['gia_niem_yet'], 0, ',', '.') . ' đ/tháng' : 'Liên hệ');
                @endphp
                <article class="relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition">
                  <span class="absolute top-4 left-4 bg-indigo-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">Top {{ $loop->iteration }}</span>
                  <img src="{{ $thumb }}" alt="{{ $item['tieu_de'] ?? 'Phòng trọ' }}" class="w-full h-48 object-cover">
                  <div class="p-5 space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $item['tieu_de'] ?? 'Phòng trọ' }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-300 flex items-center gap-1">
                      <i class="ri-map-pin-line text-purple-500"></i>
                      {{ $item['dia_chi'] ?? 'Đang cập nhật' }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                      @forelse($tags as $tag)
                        <span class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full">{{ trim($tag) }}</span>
                      @empty
                        <span class="text-xs text-gray-400">Chưa có tiện ích</span>
                      @endforelse
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-purple-700 dark:text-purple-400 text-xl font-extrabold">{{ $priceDisplay }}</p>
                        @if(!empty($item['dien_tich']))
                          <p class="text-xs text-gray-400">Diện tích: {{ $item['dien_tich'] }} m²</p>
                        @endif
                      </div>
                      <a href="{{ $detailUrl }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition">
                        Xem chi tiết
                      </a>
                    </div>
                  </div>
                </article>
              @endforeach
            </div>
          </div>
        @endif

        @if($showRecommendationsOnly ?? false)
          <p class="text-center text-gray-500">Các gợi ý đã được hiển thị ở trên. Chuyển sang tab "Tìm kiếm phòng trọ" và bấm lọc để xem danh sách đầy đủ.</p>
        @else
          @if(empty($data))
                <p class="text-center text-gray-500">Không tìm thấy bài đăng nào phù hợp.</p>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($data as $item)
                        <article
                            class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition transform hover:-translate-y-1">
                            {{-- Ảnh --}}
                            @if(!empty($item['anh_dai_dien']))
                                <img src="{{ $item['anh_dai_dien'] }}" alt="{{ $item['tieu_de'] }}" class="w-full h-56 object-cover">
                            @else
                                <div class="w-full h-56 flex items-center justify-center bg-gray-100 text-gray-400 italic">
                                    Chưa có ảnh
                                </div>
                            @endif

                            <div class="p-5">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white leading-snug truncate">
                                        {{ $item['tieu_de'] }}
                                    </h3>
                                    <div class="flex items-center text-yellow-500 text-sm">
                                        <i class="ri-star-fill mr-1"></i>
                                        <span>{{ number_format($item['rating'] ?? 4.2, 1) }}</span>
                                    </div>
                                </div>

                                <p class="text-gray-500 dark:text-gray-300 text-sm mb-2">
                                    <i class="ri-map-pin-line text-purple-500 mr-1"></i>{{ $item['dia_chi'] ?? 'TP.HCM' }}
                                </p>

                                <div class="flex flex-wrap gap-2 mb-4">
                                    
                                    @php
  $tienIch = $item['tien_ich'] ?? [];

  if (is_array($tienIch)) {
      $tags = array_filter(array_slice($tienIch, 0, 3));
  } else {
      $tags = array_filter(array_slice(explode(',', (string) $tienIch), 0, 3));
  }
@endphp

                               
                                    @forelse($tags as $tag)
                                        <span class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full">{{ trim($tag) }}</span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Chưa có tiện ích</span>
                                    @endforelse
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-purple-700 dark:text-purple-400 font-extrabold text-lg">
                                            {{ $item['gia_hien_thi'] ?? '0 đ/tháng' }}
                                        </div>
                                        <p class="text-gray-400 text-xs">
                                            ~{{ number_format(($item['gia_niem_yet'] ?? 0) / max(($item['dien_tich'] ?? 1), 1), 0, ',', '.') }}₫/m²
                                        </p>
                                    </div>
                                    <a href="{{ url('/bai-dang/' . $item['id']) }}"
                                        class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:scale-105 transition">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>

                            <div class="border-t flex justify-around items-center py-3 text-sm text-gray-600 dark:text-gray-300">
                                    <button 
                                      onclick="showChuTroInfo(this.dataset.chuTro, this.dataset.sdtChuTro)"
                                      data-chu-tro="{{ e($item['chu_tro'] ?? 'Chưa rõ') }}"
                                      data-sdt-chu-tro="{{ e($item['sdt_chu_tro'] ?? 'Chưa có SĐT') }}"
                                      class="hover:text-purple-600 flex items-center gap-1">
                                  <i class="ri-phone-line"></i> Gọi ngay
                              </button>

                                    <button 
                                      onclick="showChuTroInfo(this.dataset.chuTro, this.dataset.sdtChuTro)"
                                      data-chu-tro="{{ e($item['chu_tro'] ?? 'Chưa rõ') }}"
                                      data-sdt-chu-tro="{{ e($item['sdt_chu_tro'] ?? 'Chưa có SĐT') }}"
                                      class="hover:text-purple-600 flex items-center gap-1">
                                  <i class="ri-chat-1-line"></i> Nhắn tin
                              </button>

                              {{-- -<button class="hover:text-purple-600 flex items-center gap-1">
                                  <i class="ri-home-heart-line"></i> Đặt phòng
                              </button> --}}
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Phân trang --}}
                @if(!empty($meta))
                    <div class="flex justify-center items-center gap-4 mt-10">
                        @if($meta['current_page'] > 1)
                            <a href="?page={{ $meta['current_page'] - 1 }}"
                                class="px-4 py-2 border rounded-lg text-indigo-600 hover:bg-indigo-50">« Trước</a>
                        @endif
                        <span class="text-gray-700 dark:text-gray-300">
                            Trang {{ $meta['current_page'] }} / {{ $meta['last_page'] }}
                        </span>
                        @if($meta['current_page'] < $meta['last_page'])
                            <a href="?page={{ $meta['current_page'] + 1 }}"
                                class="px-4 py-2 border rounded-lg text-indigo-600 hover:bg-indigo-50">Sau »</a>
                        @endif
                    </div>
                @endif
            @endif
              @endif
        </div>
        <!-- Modal thông tin chủ trọ -->
        <div id="modalChuTro" 
            class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
          <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center relative">
            <button onclick="closeModalChuTro()" 
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
              <i class="ri-close-line text-2xl"></i>
            </button>
            <i class="ri-user-3-line text-indigo-500 text-4xl mb-3"></i>
            <h2 class="text-lg font-bold text-gray-800 mb-2">Thông tin chủ trọ</h2>
            <p id="modalChuTroName" class="text-gray-700 text-base"></p>
            <p id="modalChuTroPhone" class="text-indigo-600 font-semibold text-lg mt-2"></p>
            <button onclick="closeModalChuTro()" 
                    class="mt-5 px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
              Đóng
            </button>
          </div>
        </div>
    </section>

    <script>
    function searchForm() {
        return {
            filters: {
                area: '{{ request("dia_chi") }}' || '',
                min: '{{ request("min") }}' || '',
                max: '{{ request("max") }}' || '',
                sort: '{{ request("sort") ?? "asc" }}'
            },

            // 💰 Set khoảng giá nhanh
            setPrice(min, max) {
                this.filters.min = min;
                this.filters.max = max;
            }
        }
    }
</script>
<script>
// Ensure Alpine-bound values are present on native form submit in case some browsers
// don't update the input value immediately from x-model when using datalist.
document.addEventListener('DOMContentLoaded', function(){
  var form = document.querySelector("form[action='{{ route('listing') }}']");
  if (!form) return;
  form.addEventListener('submit', function(e){
    try {
      // sync the area x-model -> input (Alpine normally does this). As a fallback,
      // if there's an element with x-data, read its filters.area and set input.value.
      var input = form.querySelector('input[name="dia_chi"]');
      if (!input) return;
      // if Alpine exposes the component, try to read it
      var alpineEl = document.querySelector('[x-data]');
      if (alpineEl && typeof Alpine !== 'undefined' && Alpine.reactive) {
        // try to access via .$data (works in Alpine v3 with component.get())
        try {
          // find component instance
          var comp = Alpine && Alpine.$data ? Alpine.$data(alpineEl) : null;
          if (comp && comp.filters && comp.filters.area !== undefined) {
            input.value = comp.filters.area;
          }
        } catch (_) {
          // ignore
        }
      }
    } catch(err) { console.debug('sync submit error', err); }
  });
});
</script>
<script>
function showChuTroInfo(ten, sdt) {
    document.getElementById('modalChuTroName').textContent = "👤 " + (ten || 'Chưa có thông tin');
    document.getElementById('modalChuTroPhone').innerHTML = "📞 <a href='tel:" + (sdt || '') + "' class='text-indigo-600 font-semibold'>" + (sdt || 'Chưa có SĐT') + "</a>";
    document.getElementById('modalChuTro').classList.remove('hidden');
    document.getElementById('modalChuTro').classList.add('flex');
}

function closeModalChuTro() {
    const modal = document.getElementById('modalChuTro');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection