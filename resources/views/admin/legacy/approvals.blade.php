@extends('admin.layout')
@section('title', 'Admin - Xét duyệt Bài viết')
@section('content')
  <h1 class="text-2xl font-semibold mb-6">Xét duyệt Bài viết</h1>

  @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
      {{ session('error') }}
    </div>
  @endif

  @if(isset($error))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
      Lỗi khi tải dữ liệu: {{ $error }}
    </div>
  @endif

  @if(isset($stats))
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg border">
      <p class="text-sm text-gray-600">Đang chờ duyệt</p>
      <p class="text-2xl font-bold text-orange-600">{{ $stats['dang_cho_duyet'] ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border">
      <p class="text-sm text-gray-600">Đã duyệt tháng này</p>
      <p class="text-2xl font-bold text-green-600">{{ $stats['da_duyet_thang_nay'] ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border">
      <p class="text-sm text-gray-600">Đã từ chối tháng này</p>
      <p class="text-2xl font-bold text-red-600">{{ $stats['da_tu_choi_thang_nay'] ?? 0 }}</p>
    </div>
  </div>
  @endif

  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold">Tiêu đề</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Người đăng</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Ngày gửi</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Địa chỉ</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @if(isset($posts) && isset($posts['data']) && count($posts['data']) > 0)
          @foreach ($posts['data'] as $post)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
              <div class="font-medium">{{ $post['tieu_de'] ?? 'N/A' }}</div>
              <div class="text-xs text-gray-500">Phòng: {{ $post['so_phong'] ?? 'N/A' }}</div>
            </td>
            <td class="px-4 py-3">
              <div>{{ $post['tac_gia'] ?? 'N/A' }}</div>
              <div class="text-xs text-gray-500">{{ $post['email'] ?? '' }}</div>
            </td>
            <td class="px-4 py-3 text-sm">
              {{ isset($post['ngay_tao']) ? date('d/m/Y', strtotime($post['ngay_tao'])) : 'N/A' }}
            </td>
            <td class="px-4 py-3 text-sm">
              <div>{{ $post['ten_day_tro'] ?? 'N/A' }}</div>
              <div class="text-xs text-gray-500">{{ $post['dia_chi'] ?? '' }}</div>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="{{ route('admin.approvals.show', $post['id']) }}" 
                 class="px-2.5 py-1.5 rounded border text-sm hover:bg-gray-50">
                Chi tiết
              </a>
              <form method="POST" action="{{ route('admin.approvals.approve', $post['id']) }}" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Xác nhận phê duyệt bài viết này?')"
                        class="px-3 py-1.5 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                  Duyệt
                </button>
              </form>
              <button type="button"
                      data-post-id="{{ $post['id'] }}" 
                      data-post-title="{{ $post['tieu_de'] }}"
                      class="reject-btn px-3 py-1.5 rounded bg-red-600 text-white text-sm hover:bg-red-700">
                Từ chối
              </button>
            </td>
          </tr>
          @endforeach
        @else
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
              Không có bài viết nào đang chờ duyệt
            </td>
          </tr>
        @endif
      </tbody>
    </table>

    @if(isset($posts['links']) && count($posts['data']) > 0)
    <div class="px-4 py-3 border-t">
      <div class="flex justify-between items-center">
        <div class="text-sm text-gray-600">
          Hiển thị {{ $posts['from'] ?? 0 }} - {{ $posts['to'] ?? 0 }} trong tổng số {{ $posts['total'] ?? 0 }}
        </div>
      </div>
    </div>
    @endif
  </div>

  <!-- Modal từ chối -->
  <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 pointer-events-auto" id="rejectModalContent">
      <h3 class="text-lg font-semibold mb-4">Từ chối bài viết</h3>
  <form id="rejectForm" method="POST" action="" data-no-ajax="true">
        @csrf
        <input type="hidden" name="post_id" id="reject_post_id">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Lý do từ chối:</label>
          <textarea name="ly_do" 
                    required 
                    rows="4" 
                    class="w-full border rounded px-3 py-2"
                    placeholder="Nhập lý do từ chối..."></textarea>
        </div>
        <div class="flex space-x-2 justify-end">
          <button type="button" 
                  onclick="hideRejectModal()"
                  class="px-4 py-2 border rounded hover:bg-gray-50">
            Hủy
          </button>
    <button type="submit" data-no-ajax="true"
      class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Xác nhận từ chối
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Xử lý nút từ chối
    document.querySelectorAll('.reject-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const postId = this.dataset.postId;
        const postTitle = this.dataset.postTitle;
        showRejectModal(postId, postTitle);
      });
    });

    function showRejectModal(postId, postTitle) {
      console.debug('showRejectModal', postId, postTitle);
      document.getElementById('reject_post_id').value = postId;
      document.getElementById('rejectForm').action = `/admin/approvals/${postId}/reject`;
      document.getElementById('rejectModal').classList.remove('hidden');

      const content = document.getElementById('rejectModalContent');
      if (content && !content.dataset.debugAttached) {
        content.addEventListener('click', function(e) {
          console.debug('rejectModalContent clicked', e.target);
        });
        content.dataset.debugAttached = '1';
      }
    }

    function hideRejectModal() {
      document.getElementById('rejectModal').classList.add('hidden');
    }

    document.getElementById('rejectModal').addEventListener('click', function(e) {
      if (e.target === this) {
        hideRejectModal();
      }
    });
  </script>
@endsection
@extends('admin.layout')
@section('title', 'Admin - Xét duyệt Bài viết')
@section('content')
  <h1 class="text-2xl font-semibold mb-6">Xét duyệt Bài viết</h1>

  @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
      {{ session('error') }}
    </div>
  @endif

  @if(isset($error))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
      Lỗi khi tải dữ liệu: {{ $error }}
    </div>
  @endif

  @if(isset($stats))
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg border">
      <p class="text-sm text-gray-600">Đang chờ duyệt</p>
      <p class="text-2xl font-bold text-orange-600">{{ $stats['dang_cho_duyet'] ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border">
      <p class="text-sm text-gray-600">Đã duyệt tháng này</p>
      <p class="text-2xl font-bold text-green-600">{{ $stats['da_duyet_thang_nay'] ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border">
      <p class="text-sm text-gray-600">Đã từ chối tháng này</p>
      <p class="text-2xl font-bold text-red-600">{{ $stats['da_tu_choi_thang_nay'] ?? 0 }}</p>
    </div>
  </div>
  @endif

  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold">Tiêu đề</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Người đăng</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Ngày gửi</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Địa chỉ</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @if(isset($posts) && isset($posts['data']) && count($posts['data']) > 0)
          @foreach ($posts['data'] as $post)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
              <div class="font-medium">{{ $post['tieu_de'] ?? 'N/A' }}</div>
              <div class="text-xs text-gray-500">Phòng: {{ $post['so_phong'] ?? 'N/A' }}</div>
            </td>
            <td class="px-4 py-3">
              <div>{{ $post['tac_gia'] ?? 'N/A' }}</div>
              <div class="text-xs text-gray-500">{{ $post['email'] ?? '' }}</div>
            </td>
            <td class="px-4 py-3 text-sm">
              {{ isset($post['ngay_tao']) ? date('d/m/Y', strtotime($post['ngay_tao'])) : 'N/A' }}
            </td>
            <td class="px-4 py-3 text-sm">
              <div>{{ $post['ten_day_tro'] ?? 'N/A' }}</div>
              <div class="text-xs text-gray-500">{{ $post['dia_chi'] ?? '' }}</div>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="{{ route('admin.approvals.show', $post['id']) }}" 
                 class="px-2.5 py-1.5 rounded border text-sm hover:bg-gray-50">
                Chi tiết
              </a>
              <form method="POST" action="{{ route('admin.approvals.approve', $post['id']) }}" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Xác nhận phê duyệt bài viết này?')"
                        class="px-3 py-1.5 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                  Duyệt
                </button>
              </form>
              <button type="button"
                      data-post-id="{{ $post['id'] }}" 
                      data-post-title="{{ $post['tieu_de'] }}"
                      class="reject-btn px-3 py-1.5 rounded bg-red-600 text-white text-sm hover:bg-red-700">
                Từ chối
              </button>
            </td>
          </tr>
          @endforeach
        @else
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
              Không có bài viết nào đang chờ duyệt
            </td>
          </tr>
        @endif
      </tbody>
    </table>

    @if(isset($posts['links']) && count($posts['data']) > 0)
    <div class="px-4 py-3 border-t">
      <div class="flex justify-between items-center">
        <div class="text-sm text-gray-600">
          Hiển thị {{ $posts['from'] ?? 0 }} - {{ $posts['to'] ?? 0 }} trong tổng số {{ $posts['total'] ?? 0 }}
        </div>
      </div>
    </div>
    @endif
  </div>

  <!-- Modal từ chối -->
  <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 pointer-events-auto" id="rejectModalContent">
      <h3 class="text-lg font-semibold mb-4">Từ chối bài viết</h3>
  <form id="rejectForm" method="POST" action="" data-no-ajax="true">
        @csrf
        <input type="hidden" name="post_id" id="reject_post_id">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Lý do từ chối:</label>
          <textarea name="ly_do" 
                    required 
                    rows="4" 
                    class="w-full border rounded px-3 py-2"
                    placeholder="Nhập lý do từ chối..."></textarea>
        </div>
        <div class="flex space-x-2 justify-end">
          <button type="button" 
                  onclick="hideRejectModal()"
                  class="px-4 py-2 border rounded hover:bg-gray-50">
            Hủy
          </button>
    <button type="submit" data-no-ajax="true"
      class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Xác nhận từ chối
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Xử lý nút từ chối
    document.querySelectorAll('.reject-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const postId = this.dataset.postId;
        const postTitle = this.dataset.postTitle;
        showRejectModal(postId, postTitle);
      });
    });

    function showRejectModal(postId, postTitle) {
      console.debug('showRejectModal', postId, postTitle);
      document.getElementById('reject_post_id').value = postId;
      document.getElementById('rejectForm').action = `/admin/approvals/${postId}/reject`;
      document.getElementById('rejectModal').classList.remove('hidden');

      const content = document.getElementById('rejectModalContent');
      if (content && !content.dataset.debugAttached) {
        content.addEventListener('click', function(e) {
          console.debug('rejectModalContent clicked', e.target);
        });
        content.dataset.debugAttached = '1';
      }
    }

    function hideRejectModal() {
      document.getElementById('rejectModal').classList.add('hidden');
    }

    document.getElementById('rejectModal').addEventListener('click', function(e) {
      if (e.target === this) {
        hideRejectModal();
      }
    });
  </script>
@endsection
