@extends('admin.layout')
@section('title', 'Xét duyệt - Chi tiết')
@section('content')
  @if(isset($post) && is_array($post))
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Xét duyệt - Chi tiết bài viết</h1>
      <a href="{{ route('admin.approvals') }}" class="px-4 py-2 rounded-lg border hover:bg-gray-50">&larr; Quay lại</a>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="grid grid-cols-1 gap-6">
        <div>
          <div class="bg-gray-50 rounded p-3 mb-4">
            @if(!empty($post['anh_dai_dien']))
              <img id="post-main-image" src="{{ $post['anh_dai_dien'] }}" alt="Ảnh bài viết" class="w-full h-64 object-cover rounded border">
            @else
              <div class="w-full h-64 bg-gray-100 rounded flex items-center justify-center text-gray-400">Không có ảnh</div>
            @endif
          </div>

          @if(!empty($post['images']) && is_array($post['images']))
            <div class="mt-2 grid grid-cols-4 gap-2">
              @foreach($post['images'] as $img)
                @php $url = is_array($img) ? ($img['url'] ?? '') : ($img->url ?? '');
                     $imgId = is_array($img) ? ($img['id'] ?? '') : ($img->id ?? ''); @endphp
                @if(!empty($url))
                  <div class="relative">
                    <button type="button" class="thumbnail-btn w-full h-20 overflow-hidden rounded" data-url="{{ $url }}">
                      <img src="{{ $url }}" class="w-full h-20 object-cover rounded" alt="thumb">
                    </button>
                    <label class="absolute top-1 right-1 bg-white rounded-full p-1 shadow">
                      <input type="checkbox" class="reject-image-checkbox" value="{{ $imgId }}" title="Đánh dấu từ chối">
                    </label>
                  </div>
                @endif
              @endforeach
            </div>
          @endif
        </div>

        <div>
          <h2 class="text-xl font-semibold mb-2">{{ $post['tieu_de'] ?? '---' }}</h2>
          <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ $post['mo_ta'] ?? '' }}</p>

          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <div class="text-xs text-gray-500">Giá</div>
              <div class="text-lg font-medium">{{ isset($post['gia_niem_yet']) ? number_format($post['gia_niem_yet']) . ' đ' : 'N/A' }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500">Diện tích</div>
              <div class="text-lg font-medium">{{ $post['dien_tich'] ?? '-' }} m²</div>
            </div>
          </div>

          @if(!empty($post['tien_ich']) && is_array($post['tien_ich']))
            <div class="mt-6">
              <div class="text-sm text-gray-500 mb-2">Tiện ích</div>
              <div class="flex flex-wrap gap-2">
                @foreach($post['tien_ich'] as $t)
                  <span class="px-3 py-1 bg-gray-100 rounded text-sm">{{ is_array($t) ? ($t['ten_tien_ich'] ?? ($t['ten'] ?? '')) : ($t->ten_tien_ich ?? $t->ten ?? '') }}</span>
                @endforeach
              </div>
            </div>
          @endif

          <div class="mt-6 flex items-center justify-center gap-4">
            <form method="POST" action="{{ route('admin.approvals.approve', $post['id']) }}">
              @csrf
              <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Duyệt</button>
            </form>

            <button id="rejectBtn" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">Từ chối</button>
          </div>

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
@extends('admin.layout')
@section('title', 'Xét duyệt - Chi tiết')
@section('content')
  @if(isset($post) && is_array($post))
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Xét duyệt - Chi tiết bài viết</h1>
      <a href="{{ route('admin.approvals') }}" class="px-4 py-2 rounded-lg border hover:bg-gray-50">&larr; Quay lại</a>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="grid grid-cols-1 gap-6">
        <div>
          <div class="bg-gray-50 rounded p-3 mb-4">
            @if(!empty($post['anh_dai_dien']))
              <img id="post-main-image" src="{{ $post['anh_dai_dien'] }}" alt="Ảnh bài viết" class="w-full h-64 object-cover rounded border">
            @else
              <div class="w-full h-64 bg-gray-100 rounded flex items-center justify-center text-gray-400">Không có ảnh</div>
            @endif
          </div>

          @if(!empty($post['images']) && is_array($post['images']))
            <div class="mt-2 grid grid-cols-4 gap-2">
              @foreach($post['images'] as $img)
                @php $url = is_array($img) ? ($img['url'] ?? '') : ($img->url ?? '');
                     $imgId = is_array($img) ? ($img['id'] ?? '') : ($img->id ?? ''); @endphp
                @if(!empty($url))
                  <div class="relative">
                    <button type="button" class="thumbnail-btn w-full h-20 overflow-hidden rounded" data-url="{{ $url }}">
                      <img src="{{ $url }}" class="w-full h-20 object-cover rounded" alt="thumb">
                    </button>
                    <label class="absolute top-1 right-1 bg-white rounded-full p-1 shadow">
                      <input type="checkbox" class="reject-image-checkbox" value="{{ $imgId }}" title="Đánh dấu từ chối">
                    </label>
                  </div>
                @endif
              @endforeach
            </div>
          @endif
        </div>

        <div>
          <h2 class="text-xl font-semibold mb-2">{{ $post['tieu_de'] ?? '---' }}</h2>
          <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ $post['mo_ta'] ?? '' }}</p>

          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <div class="text-xs text-gray-500">Giá</div>
              <div class="text-lg font-medium">{{ isset($post['gia_niem_yet']) ? number_format($post['gia_niem_yet']) . ' đ' : 'N/A' }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500">Diện tích</div>
              <div class="text-lg font-medium">{{ $post['dien_tich'] ?? '-' }} m²</div>
            </div>
          </div>

          @if(!empty($post['tien_ich']) && is_array($post['tien_ich']))
            <div class="mt-6">
              <div class="text-sm text-gray-500 mb-2">Tiện ích</div>
              <div class="flex flex-wrap gap-2">
                @foreach($post['tien_ich'] as $t)
                  <span class="px-3 py-1 bg-gray-100 rounded text-sm">{{ is_array($t) ? ($t['ten_tien_ich'] ?? ($t['ten'] ?? '')) : ($t->ten_tien_ich ?? $t->ten ?? '') }}</span>
                @endforeach
              </div>
            </div>
          @endif

          <div class="mt-6 flex items-center justify-center gap-4">
            <form method="POST" action="{{ route('admin.approvals.approve', $post['id']) }}">
              @csrf
              <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Duyệt</button>
            </form>

            <button id="rejectBtn" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">Từ chối</button>
          </div>

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
