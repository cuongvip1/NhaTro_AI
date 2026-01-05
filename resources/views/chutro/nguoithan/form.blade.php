@extends('layouts.chu-tro')

@section('title', 'Thêm người thân')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4">📝 Thêm người thân sống cùng</h4>

        {{-- Hiển thị thông báo nếu có --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('chu-tro.nguoi-than.store') }}">
                    @csrf

                    <input type="hidden" name="khach_thue_id" value="{{ $khachThueId }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ho_ten" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" id="ho_ten" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="moi_quan_he" class="form-label">Quan hệ <span class="text-danger">*</span></label>
                            <input type="text" name="moi_quan_he" id="moi_quan_he" class="form-control" required
                                placeholder="VD: Vợ, Chồng, Con, Bạn...">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="so_cccd" class="form-label">Số CCCD / CMND</label>
                            <input type="text" name="so_cccd" id="so_cccd" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" id="ngay_sinh" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nghe_nghiep" class="form-label">Nghề nghiệp</label>
                            <input type="text" name="nghe_nghiep" id="nghe_nghiep" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                            <input type="text" name="so_dien_thoai" id="so_dien_thoai" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">
                            💾 Lưu người thân
                        </button>
                        <a href="{{ route('chu-tro.nguoi-than.index', $khachThueId) }}" class="btn btn-secondary">
                            ⬅ Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection