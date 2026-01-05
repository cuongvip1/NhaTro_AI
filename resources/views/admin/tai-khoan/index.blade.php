@extends('admin.layout')
@section('title', 'Admin - Quản lý Tài khoản')
@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Quản lý Tài khoản</h1>
    <a href="#" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"><i class="ri-add-line mr-1"></i> Thêm tài khoản</a>
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

  @if(isset($stats))
    <div class="grid grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg border">
        <div class="text-sm text-gray-600">Tổng tài khoản</div>
        <div class="text-2xl font-bold">{{ $stats['tong_tai_khoan'] ?? 0 }}</div>
      </div>
      <div class="bg-white p-4 rounded-lg border">
        <div class="text-sm text-gray-600">Chủ trọ</div>
        <div class="text-2xl font-bold">{{ $stats['theo_vai_tro']['chu_tro'] ?? 0 }}</div>
      </div>
      <div class="bg-white p-4 rounded-lg border">
        <div class="text-sm text-gray-600">Khách thuê</div>
        <div class="text-2xl font-bold">{{ $stats['theo_vai_tro']['khach_thue'] ?? 0 }}</div>
      </div>
      <div class="bg-white p-4 rounded-lg border">
        <div class="text-sm text-gray-600">Mới tháng này</div>
        <div class="text-2xl font-bold text-green-600">{{ $stats['moi_trong_thang'] ?? 0 }}</div>
      </div>
    </div>
  @endif

  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold">Họ tên</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Email</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Vai trò</th>
          <th class="px-4 py-3 text-left text-sm font-semibold">Trạng thái</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @if(isset($accounts) && isset($accounts['data']) && count($accounts['data']) > 0)
          @foreach ($accounts['data'] as $acc)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">{{ $acc['ho_ten'] ?? 'N/A' }}</td>
            <td class="px-4 py-3">{{ $acc['email'] ?? 'N/A' }}</td>
            <td class="px-4 py-3">
              @php
                $roleText = match($acc['vai_tro'] ?? '') {
                  'quan_tri' => 'Admin',
                  'chu_tro' => 'Chủ trọ',
                  'khach_thue' => 'Khách thuê',
                  default => 'N/A'
                };
              @endphp
              <span class="capitalize">{{ $roleText }}</span>
            </td>
            <td class="px-4 py-3">
              @php
                $tt = $acc['trang_thai'] ?? '';
                if($tt === 'hoat_dong') {
                  $statusClass = 'bg-green-100 text-green-700';
                  $statusText = 'Hoạt động';
                } elseif(in_array($tt, ['da_khoa','khoa','da-khoa'])) {
                  $statusClass = 'bg-red-100 text-red-700';
                  $statusText = 'Đã khóa';
                } elseif($tt === 'chua_xac_thuc') {
                  $statusClass = 'bg-yellow-100 text-yellow-700';
                  $statusText = 'Chưa xác thực';
                } else {
                  $statusClass = 'bg-gray-100 text-slate-600';
                  $statusText = ucfirst($tt ?: 'N/A');
                }
              @endphp
              <span class="px-2.5 py-1 rounded text-xs {{ $statusClass }}">{{ $statusText }}</span>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <button type="button"
                      data-acc-id="{{ $acc['id'] }}"
                      data-acc-name="{{ $acc['ho_ten'] }}"
                      data-acc-role="{{ $acc['vai_tro'] }}"
                      class="edit-btn px-2.5 py-1.5 rounded border text-sm hover:bg-gray-50">
                Sửa vai trò
              </button>
              <form method="POST" action="{{ route('admin.accounts.status', $acc['id']) }}" class="inline status-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="acc_name" value="{{ $acc['ho_ten'] }}">
                <select name="trang_thai" class="status-select border rounded px-2 py-1 text-sm" onchange="this.form.submit()">
                  <option value="hoat_dong" {{ ($acc['trang_thai'] ?? '') === 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                  <option value="da_khoa" {{ in_array($acc['trang_thai'] ?? '', ['da_khoa','khoa','da-khoa']) ? 'selected' : '' }}>Đã khóa</option>
                  <option value="chua_xac_thuc" {{ ($acc['trang_thai'] ?? '') === 'chua_xac_thuc' ? 'selected' : '' }}>Chưa xác thực</option>
                </select>
              </form>

              <form method="POST" action="{{ route('admin.accounts.delete', $acc['id']) }}" class="inline delete-form">
                @csrf
                @method('DELETE')
                <input type="hidden" name="acc_name" value="{{ $acc['ho_ten'] }}">
                <button type="submit" 
                        class="px-2.5 py-1.5 rounded border text-sm text-red-600 border-red-200 hover:bg-red-50">
                  Xóa
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
              Không có dữ liệu tài khoản
            </td>
          </tr>
        @endif
      </tbody>
    </table>

    @if(isset($accounts) && isset($accounts['total']) && $accounts['total'] > 0)
      <div class="px-4 py-3 border-t bg-gray-50 text-sm text-gray-600 flex items-center justify-between">
        <div>Hiển thị {{ count($accounts['data']) }} / {{ $accounts['total'] }} tài khoản</div>
        @if(isset($accounts['current_page']) && isset($accounts['last_page']))
          <div class="flex items-center space-x-3">
            @if($accounts['current_page'] > 1)
              <a href="{{ request()->fullUrlWithQuery(['page' => $accounts['current_page'] - 1]) }}" class="inline-block px-4 py-2 border rounded-lg text-indigo-600 bg-white">« Trước</a>
            @else
              <button class="inline-block px-4 py-2 border rounded-lg text-gray-400 bg-gray-100" disabled>« Trước</button>
            @endif

            <div class="text-gray-700">Trang {{ $accounts['current_page'] }} / {{ $accounts['last_page'] }}</div>

            @if($accounts['current_page'] < $accounts['last_page'])
              <a href="{{ request()->fullUrlWithQuery(['page' => $accounts['current_page'] + 1]) }}" class="inline-block px-4 py-2 border rounded-lg text-indigo-600 bg-white">Sau »</a>
            @else
              <button class="inline-block px-4 py-2 border rounded-lg text-gray-400 bg-gray-100" disabled>Sau »</button>
            @endif
          </div>
        @endif
      </div>
    @endif
  </div>

  <!-- Modal Sửa Vai Trò -->
  <div id="editRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 pointer-events-auto" id="editRoleModalContent">
      <h3 class="text-lg font-semibold mb-4">Sửa vai trò người dùng</h3>
  <form id="editRoleForm" method="POST" action="" data-no-ajax="true">
        @csrf
        @method('PATCH')
        <input type="hidden" name="account_id" id="edit_account_id">
        
        <div class="mb-4">
          <p class="text-sm text-gray-600 mb-2">Tài khoản: <span id="edit_account_name" class="font-semibold"></span></p>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Vai trò:</label>
          <select name="vai_tro" required class="w-full border rounded px-3 py-2">
            <option value="khach_thue">Khách thuê</option>
            <option value="chu_tro">Chủ trọ</option>
            <option value="quan_tri">Admin</option>
          </select>
        </div>

        <div class="flex space-x-2 justify-end">
          <button type="button" 
                  id="editRoleCancelBtn"
                  class="px-4 py-2 border rounded hover:bg-gray-50">
            Hủy
          </button>
    <button type="submit" data-no-ajax="true"
      class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Cập nhật
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Xử lý form khóa/mở tài khoản
    document.querySelectorAll('.status-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        const accName = this.querySelector('input[name="acc_name"]').value;
        const actionText = this.querySelector('input[name="action_text"]').value;
        
        if (!confirm(`Xác nhận ${actionText} tài khoản "${accName}"?`)) {
          e.preventDefault();
        }
      });
    });

    // Xử lý form Xóa tài khoản
    document.querySelectorAll('.delete-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const accName = this.querySelector('input[name="acc_name"]').value;
        const action = this.action;
        if (!confirm(`Xác nhận xóa tài khoản "${accName}"? Hành động không thể hoàn tác.`)) {
          return;
        }

        // Prepare data (include _method=DELETE and CSRF token)
        const token = this.querySelector('input[name="_token"]').value;
        const formData = new URLSearchParams();
        formData.append('_token', token);
        formData.append('_method', 'DELETE');

        // Optional: include acc_name for server logs
        const accNameInput = this.querySelector('input[name="acc_name"]');
        if (accNameInput) formData.append('acc_name', accNameInput.value);

        // Perform AJAX request and remove row on success
        fetch(action, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token
          },
          body: formData.toString(),
          credentials: 'same-origin'
        }).then(r => r.json().catch(() => ({}))).then(json => {
          // If server returned success flag or no error, remove row and show message
          if (json && json.success) {
            const row = form.closest('tr');
            if (row) row.remove();
            alert('Đã xóa tài khoản thành công');
          } else if (json && json.error) {
            alert('Lỗi xóa: ' + json.error);
          } else {
            // Fallback: reload page to let server-side redirect handle flash
            window.location.reload();
          }
        }).catch(err => {
          console.error('Delete error', err);
          // fallback: submit normal form
          this.submit();
        });
      });
    });

    // Xử lý nút sửa vai trò
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const accId = this.dataset.accId;
        const accName = this.dataset.accName;
        const accRole = this.dataset.accRole;
        
        console.debug('open editRoleModal', accId, accName, accRole);
        document.getElementById('edit_account_id').value = accId;
        document.getElementById('edit_account_name').textContent = accName;
        document.getElementById('editRoleForm').action = `/admin/accounts/${accId}/role`;
        document.querySelector('select[name="vai_tro"]').value = accRole;
        
        document.getElementById('editRoleModal').classList.remove('hidden');

        const content = document.getElementById('editRoleModalContent');
        if (content && !content.dataset.debugAttached) {
          content.addEventListener('click', function(e) {
            console.debug('editRoleModalContent clicked', e.target);
          });
          content.dataset.debugAttached = '1';
        }
      });
    });

    // Robust modal close helper
    window.hideEditRoleModal = function() {
      const modal = document.getElementById('editRoleModal');
      if (!modal) return;
      // remove visible classes and add hidden (works with Tailwind/utility classes)
      modal.classList.add('hidden');
    }

    // Delegated click handler: works even if button is re-rendered
    document.addEventListener('click', function (e) {
      const cancel = e.target.closest && e.target.closest('#editRoleCancelBtn');
      if (cancel) {
        e.preventDefault();
        window.hideEditRoleModal();
      }

      // allow clicking elements with data-close-modal attribute to close
      const closeTrigger = e.target.closest && e.target.closest('[data-close-modal]');
      if (closeTrigger) {
        e.preventDefault();
        window.hideEditRoleModal();
      }
    }, true);

    // Close modal on ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' || e.key === 'Esc') {
        window.hideEditRoleModal();
      }
    });

    // Đóng modal khi click bên ngoài
    document.getElementById('editRoleModal').addEventListener('click', function(e) {
      if (e.target === this) {
        hideEditRoleModal();
      }
    });
  </script>
@endsection
