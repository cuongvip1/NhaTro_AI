@extends('admin.layout')
@section('title', 'Sửa Bài viết')
@section('content')
  <div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Sửa bài viết</h1>
    <a href="{{ route('admin.posts') }}" class="px-4 py-2 rounded-lg border hover:bg-gray-50">&larr; Quay lại</a>
  </div>

  @if(isset($post) && is_array($post))
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="col-span-1">
          {{-- Image gallery / main image --}}
          <div class="mb-4 bg-gray-50 rounded p-3">
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
                  <button type="button" class="thumbnail-btn border rounded overflow-hidden focus:outline-none focus:ring-2 focus:ring-indigo-300" data-url="{{ $url }}">
                    <img src="{{ $url }}" class="w-full h-20 object-cover" alt="thumb">
                  </button>
                @endif
              @endforeach
            </div>
          @endif
            {{-- Upload form for admin to add images --}}
            @if(!empty($post['id']))
              <form method="POST" action="{{ route('admin.posts.upload', $post['id']) }}" enctype="multipart/form-data" class="mt-4">
                @csrf
                <label class="block text-sm text-gray-600 mb-2">Thêm ảnh</label>
                <input type="file" name="anh[]" multiple accept="image/*" class="block mb-2">
                <div class="flex justify-end">
                  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Upload</button>
                </div>
              </form>
            @else
              <div class="mt-4 text-sm text-gray-500">Không có ID bài viết — không thể upload ảnh.</div>
            @endif
        </div>

        <div class="col-span-2">
          @if(!empty($post['id']))
            <form method="POST" action="{{ route('admin.posts.update', $post['id']) }}" data-no-ajax="true">
            @csrf
            @method('PATCH')
            <div class="mb-4">
              <label class="block text-sm font-medium mb-2">Tiêu đề</label>
              <input type="text" name="tieu_de" value="{{ $post['tieu_de'] ?? '' }}" class="w-full border rounded px-3 py-2 shadow-sm focus:ring-1 focus:ring-indigo-200">
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-2">Mô tả</label>
              <textarea name="mo_ta" rows="8" class="w-full border rounded px-3 py-2 shadow-sm focus:ring-1 focus:ring-indigo-200">{{ $post['mo_ta'] ?? '' }}</textarea>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-2">Trạng thái</label>
              @php
                $current = $post['trang_thai'] ?? ''; 
              @endphp
              <select name="trang_thai" class="border rounded px-3 py-2">
                <option value="">-- Giữ nguyên --</option>
                <option value="nhap" {{ $current === 'nhap' ? 'selected' : '' }}>Nháp</option>
                <option value="dang" {{ $current === 'dang' ? 'selected' : '' }}>Đang hiển thị</option>
                <option value="cho_duyet" {{ $current === 'cho_duyet' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="an" {{ $current === 'an' ? 'selected' : '' }}>Ẩn</option>
                <option value="tu_choi" {{ $current === 'tu_choi' ? 'selected' : '' }}>Từ chối / Ẩn</option>
              </select>
            </div>
            <div class="flex space-x-2 justify-end">
              <a href="{{ route('admin.posts') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Hủy</a>
              <button type="submit" data-no-ajax="true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu</button>
            </div>
            </form>
          @else
            <div class="p-4 bg-yellow-50 border border-yellow-100 rounded text-sm text-yellow-700">Không có ID bài viết, không thể chỉnh sửa.</div>
          @endif
        </div>
      </div>
    </div>
    <script>
      (function(){
        var form = document.querySelector('form[data-no-ajax="true"][action*="/admin/posts/"]');
        if (!form) return;
        form.addEventListener('submit', function(e){
          try {
            console.debug('post-edit form submit', {action: form.action});
            var ov = document.createElement('div');
            ov.id = 'admin-debug-overlay';
            ov.style.position = 'fixed';
            ov.style.inset = '0';
            ov.style.background = 'rgba(255,255,255,0.6)';
            ov.style.zIndex = 99999;
            ov.style.display = 'flex';
            ov.style.alignItems = 'center';
            ov.style.justifyContent = 'center';
            ov.innerHTML = '<div style="padding:12px 18px;background:#111;color:#fff;border-radius:8px;">Đang gửi... (debug)</div>';
            document.body.appendChild(ov);
          } catch(err) { console.debug('submit debug error', err); }
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
      })();
    </script>
  @else
    <div class="p-6 bg-white rounded shadow-sm">Không có dữ liệu bài viết.</div>
  @endif
@endsection
