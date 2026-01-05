<div class="container mx-auto p-4">

        <h1 class="text-2xl font-semibold mb-4">Loại nhà trọ quan tâm</h1>

    {{-- Nếu người dùng vừa được chuyển hướng từ trang đăng nhập vì chưa có sở thích, hiển thị banner giải thích --}}
    @if(session()->has('force_preference'))
        <div class="mb-4 p-3 rounded border-l-4 border-indigo-500 bg-indigo-50 text-indigo-800">
            <strong>Bạn vui lòng nhập:</strong>
            Thông tin trọ mà bạn quan tâm.
        </div>
    @endif

    <!-- Description removed per request -->

        {{-- Form cho khách thêm / sửa sở thích (mỗi khách chỉ có 1) --}}
        @php $isEdit = !empty($existing); @endphp
        <div id="so-thich-alert"></div>
        <form id="so-thich-form" class="mb-6 space-y-3 js-pane-form" method="POST" action="{{ url('/khach-thue/loai-nha-tro-quan-tam') }}">
            @csrf
            @if($isEdit)
                {{-- method override to allow PUT via FormData --}}
                <input type="hidden" name="_method" value="PUT">
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="text-sm text-gray-600">Địa chỉ</label>
                    <select name="dia_chi_id" class="w-full mt-1 rounded border-gray-200">
                        <option value="">-- Chọn khu vực --</option>
                        @foreach(($regions ?? collect()) as $r)
                            <option value="{{ $r->id }}" @if(old('dia_chi_id', $existing->dia_chi_id ?? null) == $r->id) selected @endif>{{ $r->ten_dia_chi }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('dia_chi_id'))
                        <p class="text-sm text-red-600 mt-1">{{ $errors->first('dia_chi_id') }}</p>
                    @endif
                </div>

                <div>
                    <label class="text-sm text-gray-600">Tiện ích</label>
                    <div class="mt-1">
                        <button type="button" data-target="ti-ids" class="js-toggle-list inline-flex items-center px-3 py-1 border rounded bg-white hover:bg-gray-50 cursor-pointer" tabindex="0">
                            Chọn tiện ích <span class="ml-2 text-sm text-gray-500">(<span class="js-count-ti">0</span>)</span>
                        </button>
                        <div id="ti-ids" class="hidden grid grid-cols-2 gap-2 max-h-44 overflow-auto mt-2 p-2 border rounded bg-gray-50 relative z-10">
                            @php
                                $selectedTi = [];
                                if(old('tien_ich_id')) $selectedTi = (array) old('tien_ich_id');
                                elseif(!empty($existing) && !empty($existing->tien_ich_id)) $selectedTi = explode(',', $existing->tien_ich_id);
                            @endphp
                            @foreach(($tienIch ?? collect()) as $t)
                                @php $checked = in_array((string)$t->id, array_map('strval', $selectedTi)); @endphp
                                <label class="inline-flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="tien_ich_id[]" value="{{ $t->id }}" class="form-checkbox h-4 w-4 text-indigo-600 ti-checkbox" @if($checked) checked @endif>
                                    <span class="text-sm">{{ $t->ten ?? ($t->name ?? $t->id) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-600">Dịch vụ</label>
                    <div class="mt-1">
                        <button type="button" data-target="dv-ids" class="js-toggle-list inline-flex items-center px-3 py-1 border rounded bg-white hover:bg-gray-50 cursor-pointer" tabindex="0">
                            Chọn dịch vụ <span class="ml-2 text-sm text-gray-500">(<span class="js-count-dv">0</span>)</span>
                        </button>
                        <div id="dv-ids" class="hidden grid grid-cols-2 gap-2 max-h-44 overflow-auto mt-2 p-2 border rounded bg-gray-50 relative z-10">
                            @php
                                $selectedDv = [];
                                if(old('dich_vu_id')) $selectedDv = (array) old('dich_vu_id');
                                elseif(!empty($existing) && !empty($existing->dich_vu_id)) $selectedDv = explode(',', $existing->dich_vu_id);
                            @endphp
                            @foreach(($dichVu ?? collect()) as $d)
                                @php $checkedDv = in_array((string)$d->id, array_map('strval', $selectedDv)); @endphp
                                <label class="inline-flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="dich_vu_id[]" value="{{ $d->id }}" class="form-checkbox h-4 w-4 text-indigo-600 dv-checkbox" @if($checkedDv) checked @endif>
                                    <span class="text-sm">{{ $d->ten ?? ($d->name ?? $d->id) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
                <div>
                    <label class="text-sm text-gray-600">Giá (VNĐ)</label>
                    <input name="gia" type="number" class="w-full mt-1 rounded border-gray-200" placeholder="vd: 2000000">
                </div>

                {{-- thời gian removed per request --}}

                <div class="flex items-end">
                    <button type="submit" class="ml-auto px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">@if($isEdit) Cập nhật @else Thêm sở thích @endif</button>
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-600">Ghi chú / Chi tiết</label>
                <textarea name="chi_tiet" rows="3" class="w-full mt-1 rounded border-gray-200" placeholder="Mô tả thêm về loại nhà trọ bạn muốn...">{{ old('chi_tiet', $existing->chi_tiet ?? '') }}</textarea>
                @if($errors->has('chi_tiet'))
                    <p class="text-sm text-red-600 mt-1">{{ $errors->first('chi_tiet') }}</p>
                @endif
            </div>
        </form>

        @if($items->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiện ích</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dịch vụ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi tiết</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tạo lúc</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->dia_chi ?? ($item->dia_chi_id ?? '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if(!empty($item->ten_tien_ich))
                                    {{ $item->ten_tien_ich }}
                                @elseif(!empty($item->tien_ich_id))
                                    @php
                                        $ids = is_string($item->tien_ich_id) ? explode(',', $item->tien_ich_id) : (is_array($item->tien_ich_id) ? $item->tien_ich_id : []);
                                        $names = collect($ids)->map(fn($id) => optional(($tienIch ?? collect())->firstWhere('id', (int) $id))->ten ?? $id);
                                    @endphp
                                    {{ $names->implode(', ') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if(!empty($item->ten_dich_vu))
                                    {{ $item->ten_dich_vu }}
                                @elseif(!empty($item->dich_vu_id))
                                    @php
                                        $ids2 = is_string($item->dich_vu_id) ? explode(',', $item->dich_vu_id) : (is_array($item->dich_vu_id) ? $item->dich_vu_id : []);
                                        $names2 = collect($ids2)->map(fn($id) => optional(($dichVu ?? collect())->firstWhere('id', (int) $id))->ten ?? $id);
                                    @endphp
                                    {{ $names2->implode(', ') }}
                                @else
                                    -
                                @endif
                            </td>
                            {{-- Legacy partial removed. --}}
                }
            } else if (key.startsWith('dich_vu_id')) {
                const btn = document.querySelector('.js-toggle-list[data-target="dv-ids"]');
                if (btn) {
                    btn.insertAdjacentHTML('afterend', `<p class="js-field-error text-sm text-red-600 mt-1">${message}</p>`);
                    const el = document.getElementById('dv-ids'); if (el) el.classList.remove('hidden');
                }
            } else {
                // try to find an input/select/textarea with the name
                const el = document.querySelector(`[name="${key}"]`);
                if (el) {
                    el.insertAdjacentHTML('afterend', `<p class="js-field-error text-sm text-red-600 mt-1">${message}</p>`);
                } else {
                    // fallback to top alert
                    showAlert(message, 'red');
                }
            }
        });
    }

    // initialize counts
    updateCounts();

    // toggle buttons
    document.querySelectorAll('.js-toggle-list').forEach(btn => {
        btn.addEventListener('click', function(){
            const targetId = this.dataset.target;
            const el = document.getElementById(targetId);
            if(!el) return;
            el.classList.toggle('hidden');
        });
    });

    // update counts when checkboxes change
    document.querySelectorAll('.ti-checkbox, .dv-checkbox').forEach(cb => cb.addEventListener('change', updateCounts));

});
</script>
@endpush
@endonce
