@extends('admin.layout')
@section('title', 'Admin - Quản lý Phân quyền')
@section('content')
  <h1 class="text-2xl font-semibold mb-6">Quản lý Phân quyền</h1>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      @foreach ([
        ['role' => 'admin', 'perms' => ['duyet_bai', 'quan_ly_tai_khoan', 'quan_ly_khu_vuc']],
        ['role' => 'chủ trọ', 'perms' => ['tao_bai_viet', 'sua_bai_viet']],
        ['role' => 'khách thuê', 'perms' => ['xem_bai_viet', 'binh_luan']],
      ] as $r)
      <div class="border rounded-lg p-4">
        <div class="font-medium capitalize mb-3">Vai trò: {{ $r['role'] }}</div>
        <div class="flex flex-wrap gap-2">
          @foreach ($r['perms'] as $p)
            <span class="px-2.5 py-1 rounded bg-indigo-50 text-indigo-700 text-xs">{{ $p }}</span>
          @endforeach
        </div>
        <div class="mt-4 flex gap-2">
          <button class="px-2.5 py-1.5 rounded border text-sm">Sửa</button>
          <button class="px-2.5 py-1.5 rounded border text-sm text-red-600 border-red-200">Xóa</button>
        </div>
      </div>
      @endforeach
    </div>
  </div>
@endsection
