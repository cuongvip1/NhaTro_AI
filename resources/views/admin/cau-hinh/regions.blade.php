@extends('admin.layout')
@section('title', 'Admin - Quản lý Khu vực')
@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Quản lý Khu vực</h1>
    <button id="toggle-add-region" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"><i class="ri-add-line mr-1"></i> Thêm khu vực</button>
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700">{{ session('error') }}</div>
  @endif

  {{-- Form thêm khu vực (ẩn mặc định) --}}
  <div id="add-region-form" class="mb-6 p-4 bg-white rounded border border-gray-100 shadow-sm hidden">
    <form action="{{ route('admin.regions.store') }}" method="POST">
      @csrf
      <div class="flex items-center space-x-3">
        <input type="text" name="ten_dia_chi" placeholder="Tên khu vực (ví dụ: Quận 1)" value="{{ old('ten_dia_chi') }}" required class="flex-1 border rounded px-3 py-2" />
        <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Lưu</button>
        <button type="button" id="cancel-add" class="px-4 py-2 rounded border">Hủy</button>
      </div>
      @error('ten_dia_chi')
        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
      @enderror
    </form>
  </div>

  {{-- Toggle script for add region form --}}
  @push('scripts')
    <script>
      (function(){
        var toggle = document.getElementById('toggle-add-region');
        var form = document.getElementById('add-region-form');
        var cancel = document.getElementById('cancel-add');
        if(toggle && form){
          toggle.addEventListener('click', function(){ form.classList.toggle('hidden'); });
        }
        if(cancel && form){
          cancel.addEventListener('click', function(){ form.classList.add('hidden'); });
        }
      })();
    </script>
  @endpush
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach ($regions ?? [] as $region)
      <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <div class="region-display">
            <p class="font-medium region-name">{{ $region->ten_dia_chi }}</p>
            <p class="text-xs text-gray-500 mt-1">Số bài viết: {{ $region->post_count ?? rand(0,20) }}</p>
          </div>

          <div class="space-x-2">
            {{-- Inline edit form (hidden by default) --}}
            <form action="{{ route('admin.regions.update', $region->id) }}" method="POST" class="inline-block region-edit-form hidden">
              @csrf
              @method('PUT')
              <div class="flex items-center space-x-2">
                <input type="text" name="ten_dia_chi" value="{{ $region->ten_dia_chi }}" class="border rounded px-2 py-1 text-sm" required />
                <button type="submit" class="px-2 py-1 rounded bg-green-600 text-white text-sm">Lưu</button>
                <button type="button" class="px-2 py-1 rounded border cancel-edit text-sm">Hủy</button>
              </div>
              @error('ten_dia_chi')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
            </form>

            {{-- Controls: show edit toggle and delete form --}}
            <button type="button" class="btn-edit-region px-2.5 py-1.5 rounded border text-sm" data-id="{{ $region->id }}" data-name="{{ $region->ten_dia_chi }}">Sửa</button>

            <form action="{{ route('admin.regions.destroy', $region->id) }}" method="POST" class="inline-block">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-delete-region px-2.5 py-1.5 rounded border text-sm text-red-600 border-red-200" data-name="{{ $region->ten_dia_chi }}">Xóa</button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Confirm before delete
      document.querySelectorAll('.btn-delete-region').forEach(function(btn){
        btn.addEventListener('click', function(e){
          var name = this.getAttribute('data-name') || 'khu vực này';
          if (!confirm('Bạn có chắc muốn xóa ' + name + ' ?')) {
            e.preventDefault();
          }
        });
      });

      // Toggle inline edit form
      document.querySelectorAll('.btn-edit-region').forEach(function(btn){
        btn.addEventListener('click', function(){
          var container = this.closest('.bg-white');
          if (!container) return;
          var display = container.querySelector('.region-display');
          var form = container.querySelector('.region-edit-form');
          if (form) {
            form.classList.toggle('hidden');
            if (!form.classList.contains('hidden')) {
              // hide display while editing
              if (display) display.style.display = 'none';
            } else {
              if (display) display.style.display = '';
            }
          }
        });
      });

      // Cancel edit button
      document.querySelectorAll('.cancel-edit').forEach(function(btn){
        btn.addEventListener('click', function(){
          var form = this.closest('.region-edit-form');
          if (!form) return;
          form.classList.add('hidden');
          var container = form.closest('.bg-white');
          var display = container ? container.querySelector('.region-display') : null;
          if (display) display.style.display = '';
        });
      });
    });
  </script>
@endpush
