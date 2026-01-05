@extends('admin.layout')
@section('title', 'Admin - Quản lý Bài viết')
@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Quản lý Bài viết</h1>
    <a href="#" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"><i class="ri-add-line mr-1"></i> Tạo bài viết</a>
  </div>

  @if(isset($error))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
      <strong>Lỗi:</strong> {{ $error }}
    </div>
  @endif

  @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
      {{ session('success') }}
    </div>
  @endif

  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold">Tiêu đề</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Người đăng</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Trạng thái</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @if(isset($posts) && isset($posts['data']) && count($posts['data']) > 0)
          @foreach ($posts['data'] as $post)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">{{ $post['tieu_de'] ?? 'N/A' }}</td>
            <td class="px-4 py-3">{{ $post['tac_gia'] ?? 'N/A' }}</td>
            <td class="px-4 py-3">
              @php
                $statusClass = match($post['trang_thai'] ?? '') {
                  'nhap' => 'bg-gray-100 text-gray-700',
                  'cho_duyet' => 'bg-amber-100 text-amber-700',
                  'dang' => 'bg-emerald-100 text-emerald-700',
                  'an' => 'bg-gray-100 text-gray-700',
                  'tu_choi' => 'bg-red-100 text-red-700',
                  'da_cho_thue' => 'bg-blue-100 text-blue-700',
                  default => 'bg-gray-100 text-gray-700'
                };
                $statusText = match($post['trang_thai'] ?? '') {
                  'nhap' => 'Nháp',
                  'cho_duyet' => 'Chờ duyệt',
                  'dang' => 'Hiển thị',
                  'an' => 'Ẩn',
                  'tu_choi' => 'Từ chối',
                  'da_cho_thue' => 'Đã cho thuê',
                  default => 'N/A'
                };
              @endphp
              <span class="px-2.5 py-1 rounded text-xs {{ $statusClass }}">{{ $statusText }}</span>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="{{ route('admin.posts.show', $post['id']) }}" class="view-link force-nav px-2.5 py-1.5 rounded border text-sm" data-no-ajax="true" target="_self">Xem</a>
              <form method="POST" action="{{ route('admin.posts.delete', $post['id']) }}" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài viết này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-2.5 py-1.5 rounded border text-sm text-red-600 border-red-200">Xóa</button>
              </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr>
            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
              Không có dữ liệu bài viết
            </td>
          </tr>
        @endif
      </tbody>
    </table>

    @if(isset($posts) && isset($posts['total']) && $posts['total'] > 0)
      <div class="px-4 py-3 border-t bg-gray-50 text-sm text-gray-600">
        Hiển thị {{ count($posts['data']) }} / {{ $posts['total'] }} bài viết
      </div>
    @endif
  </div>
  <script>
    (function(){
      function forceNavHandler(e) {
        try {
          var el = e.target.closest && e.target.closest('.force-nav');
          if (!el) return;
          var href = el.getAttribute('href');
          if (!href) return;
          e.preventDefault();
          e.stopImmediatePropagation();
          setTimeout(function(){ window.location.href = href; }, 10);
        } catch(err) { console.debug('forceNav error', err); }
      }

      document.addEventListener('click', forceNavHandler, true);
    })();
  </script>
@endsection
