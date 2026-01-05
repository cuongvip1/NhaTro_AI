@extends('admin.layout')
@section('title', 'Chi tiết Bài viết')
@section('content')
  @if(isset($post) && is_array($post))
  <div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Chi tiết bài viết</h1>
    <a href="{{ route('admin.posts') }}" class="px-4 py-2 rounded-lg border hover:bg-gray-50">&larr; Quay lại</a>
  </div>
  <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
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
                @php $url = is_array($img) ? ($img['url'] ?? '') : ($img->url ?? ''); @endphp
                @if(!empty($url))
                  <button type="button" class="thumbnail-btn w-full h-20 overflow-hidden rounded" data-url="{{ $url }}">
                    <img src="{{ $url }}" class="w-full h-20 object-cover rounded" alt="thumb">
                  </button>
                @endif
              @endforeach
            </div>
          @endif
        </div>

        <div>
          <h2 class="text-xl font-semibold mb-2">{{ $post['tieu_de'] ?? '---' }}</h2>
          <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ $post['mo_ta'] ?? '' }}</p>

        <div class="mb-4">
          <span class="inline-block px-3 py-1 rounded text-xs font-medium {{ 
            ($post['trang_thai'] ?? '') === 'dang' ? 'bg-emerald-100 text-emerald-800' : (
            ($post['trang_thai'] ?? '') === 'cho_duyet' ? 'bg-amber-100 text-amber-800' : (
            ($post['trang_thai'] ?? '') === 'tu_choi' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700'))
          }}">
            {{ ($post['trang_thai'] ?? '') === 'dang' ? 'Hiển thị' : (($post['trang_thai'] ?? '') === 'cho_duyet' ? 'Chờ duyệt' : (($post['trang_thai'] ?? '') === 'tu_choi' ? 'Từ chối' : 'N/A')) }}
          </span>
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
        <div class="mb-6 text-lg">
          <strong>Giá:</strong> <span class="text-2xl font-semibold text-gray-800">{{ isset($post['gia_niem_yet']) ? number_format($post['gia_niem_yet']) . ' đ' : 'N/A' }}</span>
        </div>

        <div class="mb-6">
          <h3 class="font-semibold mb-2">Mô tả</h3>
          <div class="prose max-w-none text-gray-700">{!! nl2br(e($post['mo_ta'] ?? '')) !!}</div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3 justify-center">
          <a href="{{ route('admin.posts') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Quay lại</a>

          {{-- Primary Edit button removed — keep only status edit and delete here. --}}

          {{-- Button to open status-edit modal --}}
          <button type="button" id="open-status-modal" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Sửa trạng thái</button>

          <form method="POST" action="{{ route('admin.posts.delete', $post['id']) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài viết này?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 border rounded text-red-600 hover:bg-red-50">Xóa</button>
          </form>
        </div>

        {{-- Status edit modal (hidden by default) --}}
        <div id="status-modal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-3">Sửa trạng thái bài viết</h3>
            <form method="POST" action="{{ route('admin.posts.status', $post['id']) }}">
              @csrf
              @method('PATCH')
              <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Trạng thái</label>
                <select name="trang_thai" class="border rounded px-3 py-2 w-full">
                  <option value="">-- Giữ nguyên --</option>
                  <option value="nhap" {{ ($post['trang_thai'] ?? '') === 'nhap' ? 'selected' : '' }}>Nháp</option>
                  <option value="dang" {{ ($post['trang_thai'] ?? '') === 'dang' ? 'selected' : '' }}>Đang hiển thị</option>
                  <option value="cho_duyet" {{ ($post['trang_thai'] ?? '') === 'cho_duyet' ? 'selected' : '' }}>Chờ duyệt</option>
                  <option value="an" {{ ($post['trang_thai'] ?? '') === 'an' ? 'selected' : '' }}>Ẩn</option>
                  <option value="tu_choi" {{ ($post['trang_thai'] ?? '') === 'tu_choi' ? 'selected' : '' }}>Từ chối / Ẩn</option>
                </select>
              </div>
              <div class="flex justify-end space-x-2">
                <button type="button" id="status-cancel" class="px-4 py-2 border rounded">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Lưu</button>
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
      // thumbnail click -> set main image
      document.querySelectorAll('.thumbnail-btn').forEach(btn => {
        btn.addEventListener('click', function(){
          try {
            var url = this.dataset.url;
            var main = document.getElementById('post-main-image');
            if (main && url) main.src = url;
          } catch(e){ console.debug('thumb click', e); }
        });
      });
      
      // Status modal show/hide
      document.addEventListener('DOMContentLoaded', function(){
        var open = document.getElementById('open-status-modal');
        var modal = document.getElementById('status-modal');
        var cancel = document.getElementById('status-cancel');
        if (open && modal) {
          open.addEventListener('click', function(){ modal.classList.remove('hidden'); modal.classList.add('flex'); });
        }
        if (cancel && modal) {
          cancel.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); });
        }
        // close when click outside modal content
        if (modal) modal.addEventListener('click', function(e){ if (e.target === modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); } });
      });
    </script>
  @endpush
@endsection
