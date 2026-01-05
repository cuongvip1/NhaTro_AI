@extends('admin.layout')
@section('title', 'Admin - Quản lý Khu vực')
@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Quản lý Khu vực</h1>
    <a href="#" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"><i class="ri-add-line mr-1"></i> Thêm khu vực</a>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach (['Quận 1','Quận 3','Quận 5','Quận 7','Tân Bình','Gò Vấp','Thủ Đức','Bình Thạnh','Phú Nhuận'] as $name)
      <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ $name }}</p>
            <p class="text-xs text-gray-500 mt-1">Số bài viết: {{ rand(5,20) }}</p>
          </div>
          <div class="space-x-2">
            <button class="px-2.5 py-1.5 rounded border text-sm">Sửa</button>
            <button class="px-2.5 py-1.5 rounded border text-sm text-red-600 border-red-200">Xóa</button>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
@extends('admin.layout')
@section('title', 'Admin - Quản lý Khu vực')
@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Quản lý Khu vực</h1>
    <a href="#" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"><i class="ri-add-line mr-1"></i> Thêm khu vực</a>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach (['Quận 1','Quận 3','Quận 5','Quận 7','Tân Bình','Gò Vấp','Thủ Đức','Bình Thạnh','Phú Nhuận'] as $name)
      <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ $name }}</p>
            <p class="text-xs text-gray-500 mt-1">Số bài viết: {{ rand(5,20) }}</p>
          </div>
          <div class="space-x-2">
            <button class="px-2.5 py-1.5 rounded border text-sm">Sửa</button>
            <button class="px-2.5 py-1.5 rounded border text-sm text-red-600 border-red-200">Xóa</button>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
