@extends('admin.layout')
@section('title', 'Xét duyệt - Chi tiết')
@section('content')
  @if(isset($post) && is_array($post))
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Xét duyệt - Chi tiết bài viết</h1>
      <a href="{{ route('admin.approvals') }}" class="px-4 py-2 rounded-lg border hover:bg-gray-50">&larr; Quay lại</a>
    </div>

    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Images column (left) --}}
        <div>
          @php
            $mainImg = !empty($post['anh_dai_dien']) ? $post['anh_dai_dien'] : (is_array($post['images']) && !empty($post['images'][0]['url']) ? $post['images'][0]['url'] : asset("images/no-image.png"));
          @endphp

          <div class="mb-3">
            <img id="post-main-image" src="{{ $mainImg }}" class="w-full h-[420px] object-cover rounded-xl border shadow-sm" alt="Ảnh chính">
          </div>

          @if(!empty($post['images']) && is_array($post['images']))
            <div class="grid grid-cols-4 gap-3">
              @foreach($post['images'] as $img)
                @php $url = is_array($img) ? ($img['url'] ?? '') : ($img->url ?? ''); @endphp
                @if(!empty($url))
                  <button type="button" class="thumb-btn overflow-hidden rounded-lg border focus:ring-2 focus:ring-purple-300" data-url="{{ $url }}">
                    <img src="{{ $url }}" class="w-full h-20 object-cover rounded-lg hover:scale-105 transition-transform duration-200" alt="thumb">
                  </button>
                @endif
              @endforeach
            </div>
          @endif
        </div>

        {{-- Details column (right) --}}
        <div>
          <h2 class="text-2xl font-semibold text-purple-700 mb-3">{{ $post['tieu_de'] ?? '---' }}</h2>

          @php
            // Owner name/phone may come as a string or nested array/object from the API.
            $ownerName = null;
            $ownerPhone = null;
            if (isset($post['chu_tro'])) {
                if (is_array($post['chu_tro'])) {
                    $ownerName = $post['chu_tro']['ho_ten'] ?? $post['chu_tro']['name'] ?? null;
                    $ownerPhone = $post['chu_tro']['so_dien_thoai'] ?? $post['chu_tro']['sdt'] ?? null;
                } else {
                    // may already be a string
                    $ownerName = $post['chu_tro'];
                }
            }
            // fallback to other common keys
            $ownerName = $ownerName ?? ($post['tac_gia'] ?? data_get($post, 'chuTro.ho_ten') ?? '---');
            $ownerPhone = $ownerPhone ?? ($post['sdt_chu_tro'] ?? $post['sdt'] ?? data_get($post, 'chuTro.so_dien_thoai') ?? '');
          @endphp
          <p class="text-gray-700 mb-2">
            <strong>Chủ trọ:</strong>
            <span class="text-indigo-700">{{ $ownerName }}</span>
            <span class="text-sm text-gray-500">({{ $ownerPhone }})</span>
          </p>

          <p class="text-gray-700 mb-2"><strong>Dãy trọ:</strong> {{ $post['ten_day_tro'] ?? ($post['ten_daytro'] ?? '---') }}</p>
          <p class="text-gray-700 mb-2"><strong>Phòng:</strong> {{ $post['so_phong'] ?? '---' }}</p>
          <p class="text-gray-700 mb-2"><strong>Diện tích:</strong> {{ $post['dien_tich'] ?? '---' }} m²</p>
          <p class="text-gray-700 mb-2"><strong>Tầng:</strong> {{ $post['tang'] ?? '---' }}</p>
          <p class="text-gray-700 mb-2"><strong>Sức chứa:</strong> {{ $post['suc_chua'] ?? '---' }} người</p>
          <p class="text-gray-700 mb-2"><strong>Địa chỉ:</strong> {{ $post['dia_chi'] ?? '---' }}</p>

          <p class="text-gray-700 mb-4">
            <strong>Giá niêm yết:</strong>
            <span class="text-xl font-bold text-green-700">{{ $post['gia_hien_thi'] ?? (isset($post['gia_niem_yet']) ? number_format($post['gia_niem_yet'],0,',','.') . ' đ' : '---') }}</span>
          </p>

          <p class="text-gray-700 mb-4"><strong>Ngày đăng:</strong> {{ $post['ngay_hien_thi'] ?? (isset($post['ngay_tao']) ? date('d/m/Y', strtotime($post['ngay_tao'])) : '---') }}</p>

          {{-- Rating --}}
          @if(!empty($post['rating']))
            <div class="mb-4">
              <h3 class="font-semibold text-gray-900 mb-1">Đánh giá trung bình:</h3>
              <div class="flex items-center space-x-1">
                @for($i = 1; $i <= 5; $i++)
                  @if($i <= floor($post['rating']))
                    <i class="ri-star-fill text-yellow-400 text-lg"></i>
                  @elseif($i - $post['rating'] < 1)
                    <i class="ri-star-half-fill text-yellow-400 text-lg"></i>
                  @else
                    <i class="ri-star-line text-gray-300 text-lg"></i>
                  @endif
                @endfor
                <span class="ml-2 text-gray-700">{{ number_format($post['rating'], 1) }}/5</span>
              </div>
            </div>
          @endif

          {{-- Description --}}
          <div class="mb-4">
            <h3 class="font-semibold text-gray-900 mb-1">Mô tả:</h3>
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $post['mo_ta'] ?? 'Không có mô tả.' }}</p>
          </div>

          {{-- Services --}}
          <div class="mt-4">
            <h3 class="font-semibold text-gray-900 mb-2">Dịch vụ kèm theo</h3>
            @if(!empty($post['dich_vu']) && count($post['dich_vu']) > 0)
              <table class="w-full border border-gray-200 rounded-lg text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="p-2 text-left">Tên dịch vụ</th>
                    <th class="p-2 text-left">Đơn giá</th>
                    <th class="p-2 text-left">Đơn vị</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($post['dich_vu'] as $dv)
                    <tr class="border-t">
                      <td class="p-2">{{ $dv['ten'] ?? ($dv['ten_tien_ich'] ?? '') }}</td>
                      <td class="p-2">{{ isset($dv['gia']) ? number_format($dv['gia'], 0, ',', '.') . ' đ' : '' }}</td>
                      <td class="p-2">{{ $dv['don_vi'] ?? '' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <p class="text-gray-500 italic">Chưa có dịch vụ kèm theo.</p>
            @endif
          </div>

          {{-- Approve / Reject buttons (keep unchanged) --}}
          <div class="mt-8 flex justify-center gap-4">
            <form method="POST" action="{{ route('admin.approvals.approve', $post['id']) }}">
              @csrf
              <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Duyệt</button>
            </form>

            <button id="rejectBtn" class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700">Từ chối</button>
          </div>

          {{-- Keep reject modal (unchanged) --}}
          <div id="rejectModal" class="fixed inset-0 flex items-center justify-center bg-black/30 hidden">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg">
              <h3 class="text-lg font-semibold mb-2">Lý do từ chối</h3>
              <form id="rejectForm" method="POST" action="{{ route('admin.approvals.reject', $post['id']) }}">
                @csrf
                <input type="hidden" name="rejected_image_ids" id="rejected_image_ids_input" value="">
                <div class="mb-4">
                  <textarea name="ly_do" rows="4" class="w-full border rounded px-3 py-2" required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                  <button type="button" id="cancelReject" class="px-4 py-2 border rounded">Hủy</button>
                  <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Gửi</button>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  @else
    <div class="p-6 bg-white rounded shadow-sm">Không có dữ liệu bài viết.</div>
  @endif

  @push('scripts')
    <script>
      document.getElementById('rejectBtn')?.addEventListener('click', function(){
        document.getElementById('rejectModal').classList.remove('hidden');
      });
      document.getElementById('cancelReject')?.addEventListener('click', function(){
        document.getElementById('rejectModal').classList.add('hidden');
      });

      document.getElementById('rejectForm')?.addEventListener('submit', function(e){
        const checked = Array.from(document.querySelectorAll('.reject-image-checkbox:checked')).map(cb => cb.value).filter(Boolean);
        document.getElementById('rejected_image_ids_input').value = JSON.stringify(checked);
        return true;
      });

      document.querySelectorAll('.thumbnail-btn').forEach(btn => {
        btn.addEventListener('click', function(){
          try {
            var url = this.dataset.url;
            var main = document.getElementById('post-main-image');
            if (main && url) main.src = url;
          } catch(e){ console.debug('thumb click', e); }
        });
      });
    </script>
  @endpush

@endsection
