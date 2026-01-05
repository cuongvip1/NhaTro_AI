@extends('admin.layout')
@section('title', 'Chi tiết Bài đăng')

@section('content')
  @if(isset($post) && is_array($post))
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold text-gray-800">Chi tiết bài đăng</h1>
      <a href="{{ route('admin.posts') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
        &larr; Quay lại
      </a>
    </div>

    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- 🖼 Ảnh phòng --}}
        <div>
          @php
            // Xử lý đường dẫn an toàn (dù có /storage/ hay chưa)
            $mainImg = !empty($post['anh'][0])
              ? (preg_match('/^https?:\/\//', $post['anh'][0]) ? $post['anh'][0] : asset($post['anh'][0]))
              : asset('images/no-image.png');
          @endphp

          {{-- Ảnh chính --}}
          <div class="mb-3">
            <img id="post-main-image" src="{{ $mainImg }}" class="w-full h-[420px] object-cover rounded-xl border shadow-sm"
              alt="Ảnh chính" onerror="this.src='{{ asset('images/no-image.png') }}'">
          </div>

          {{-- Ảnh nhỏ --}}
          @if(!empty($post['anh']) && count($post['anh']) > 1)
            <div class="grid grid-cols-4 gap-3">
              @foreach($post['anh'] as $url)
                @php
                  $thumb = preg_match('/^https?:\/\//', $url) ? $url : asset($url);
                @endphp
                <button type="button" class="thumb-btn overflow-hidden rounded-lg border focus:ring-2 focus:ring-purple-300">
                  <img src="{{ $thumb }}"
                    class="w-full h-20 object-cover rounded-lg hover:scale-105 transition-transform duration-200" alt="thumb"
                    onerror="this.src='{{ asset('images/no-image.png') }}'">
                </button>
              @endforeach
            </div>
          @endif
        </div>

        {{-- 📋 Thông tin chi tiết --}}
        <div>
          <h2 class="text-2xl font-semibold text-purple-700 mb-3">{{ $post['tieu_de'] ?? '---' }}</h2>

          <p class="text-gray-700 mb-2">
            <strong>Chủ trọ:</strong>
            <span class="text-indigo-700">{{ $post['chu_tro'] ?? '---' }}</span>
            <span class="text-sm text-gray-500">({{ $post['sdt_chu_tro'] ?? '' }})</span>
          </p>

          <p class="text-gray-700 mb-2"><strong>Dãy trọ:</strong> {{ $post['ten_day_tro'] ?? '---' }}</p>
          <p class="text-gray-700 mb-2"><strong>Phòng:</strong> {{ $post['so_phong'] ?? '---' }}</p>
          <p class="text-gray-700 mb-2"><strong>Diện tích:</strong> {{ $post['dien_tich'] ?? '---' }} m²</p>
          <p class="text-gray-700 mb-2"><strong>Tầng:</strong> {{ $post['tang'] ?? '---' }}</p>
          <p class="text-gray-700 mb-2"><strong>Sức chứa:</strong> {{ $post['suc_chua'] ?? '---' }} người</p>
          <p class="text-gray-700 mb-2"><strong>Địa chỉ:</strong> {{ $post['dia_chi'] ?? '---' }}</p>

          <p class="text-gray-700 mb-2">
            <strong>Giá niêm yết:</strong>
            <span class="text-xl font-bold text-green-700">{{ $post['gia_hien_thi'] ?? '---' }}</span>
          </p>

          <p class="text-gray-700 mb-4">
            <strong>Ngày đăng:</strong> {{ $post['ngay_hien_thi'] ?? '---' }}
          </p>

          {{-- ⭐ Đánh giá trung bình --}}
          <div class="mb-4">
            <h3 class="font-semibold text-gray-900 mb-1">Đánh giá trung bình:</h3>
            @if(!empty($post['rating']) && $post['rating'] > 0)
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
            @else
              <p class="text-gray-500 italic">Chưa có đánh giá</p>
            @endif
          </div>

          {{-- 📜 Mô tả --}}
          <div class="mb-4">
            <h3 class="font-semibold text-gray-900 mb-1">Mô tả:</h3>
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">
              {{ $post['mo_ta'] ?? 'Không có mô tả.' }}
            </p>
          </div>

          {{-- 🧾 Dịch vụ --}}
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
                      <td class="p-2">{{ $dv['ten'] }}</td>
                      <td class="p-2">{{ number_format($dv['gia'], 0, ',', '.') }} đ</td>
                      <td class="p-2">{{ $dv['don_vi'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <p class="text-gray-500 italic">Chưa có dịch vụ kèm theo.</p>
            @endif
          </div>
        </div>
      </div>

      {{-- 💬 Đánh giá chi tiết --}}
      @if(!empty($post['danh_gia']) && count($post['danh_gia']) > 0)
        <div class="mt-10 border-t pt-6">
          <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="ri-message-3-line text-purple-600 mr-2"></i> Đánh giá của người thuê
          </h3>
          @foreach($post['danh_gia'] as $dg)
            <div class="border-b py-3">
              <div class="flex justify-between items-center">
                <span class="font-medium text-gray-800">{{ $dg->nguoi_danh_gia ?? 'Ẩn danh' }}</span>
                <span class="text-sm text-gray-500">{{ date('d/m/Y', strtotime($dg->ngay_tao)) }}</span>
              </div>
              <div class="flex items-center mt-1">
                @for($i = 1; $i <= 5; $i++)
                  @if($i <= $dg->diem_so)
                    <i class="ri-star-fill text-yellow-400"></i>
                  @else
                    <i class="ri-star-line text-gray-300"></i>
                  @endif
                @endfor
                <span class="ml-2 text-gray-600">{{ $dg->diem_so }}/5</span>
              </div>
              @if(!empty($dg->binh_luan))
                <p class="text-gray-700 italic mt-1">“{{ $dg->binh_luan }}”</p>
              @endif
            </div>
          @endforeach
        </div>
      @endif

      {{-- ⚙️ Nút hành động (giữ lại Xóa; nút sửa nhỏ có ở danh sách bài viết để tránh trùng lặp) --}}
      <div class="mt-8 flex justify-center gap-4">
        {{-- Primary edit button removed here; only status edit & delete shown in legacy detail view. --}}

        <button type="button" id="open-status-modal-legacy" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Sửa trạng thái</button>

        <form method="POST" action="{{ route('admin.posts.delete', $post['id']) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài đăng này?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="px-5 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
            <i class="ri-delete-bin-line mr-1"></i> Xóa
          </button>
        </form>
      </div>

      {{-- Status modal for legacy detail --}}
      <div id="status-modal-legacy" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
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
              <button type="button" id="status-cancel-legacy" class="px-4 py-2 border rounded">Hủy</button>
              <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Lưu</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @else
    <div class="p-6 bg-white rounded shadow-sm text-gray-600 text-center">
      Không tìm thấy dữ liệu bài đăng.
    </div>
  @endif

  {{-- 🖱️ JS xem ảnh thumbnail --}}
  @push('scripts')
    <script>
      document.querySelectorAll('.thumb-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const main = document.getElementById('post-main-image');
          const img = btn.querySelector('img');
          if (main && img) main.src = img.src;
        });
      });
      // Status modal (legacy)
      document.addEventListener('DOMContentLoaded', function(){
        var open = document.getElementById('open-status-modal-legacy');
        var modal = document.getElementById('status-modal-legacy');
        var cancel = document.getElementById('status-cancel-legacy');
        if (open && modal) open.addEventListener('click', function(){ modal.classList.remove('hidden'); modal.classList.add('flex'); });
        if (cancel && modal) cancel.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); });
        if (modal) modal.addEventListener('click', function(e){ if (e.target === modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); } });
      });
    </script>
  @endpush
@endsection