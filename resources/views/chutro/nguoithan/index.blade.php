@extends('layouts.chu-tro')

@section('title', 'Quản lý người thân')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>👨‍👩‍👧‍👦 Danh sách người thân</h4>
            <a href="{{ route('chu-tro.nguoi-than.create', $khachThueId) }}" class="btn btn-primary">
                + Thêm người thân
            </a>
        </div>

        {{-- Thông báo thành công hoặc lỗi --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Bảng hiển thị danh sách --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Họ tên</th>
                            <th>Quan hệ</th>
                            <th>CCCD</th>
                            <th>Số điện thoại</th>
                            <th>Ngày sinh</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nguoiThan as $nt)
                            <tr>
                                <td>{{ $nt['ho_ten'] }}</td>
                                <td>{{ $nt['moi_quan_he'] }}</td>
                                <td>{{ $nt['so_cccd'] ?? '-' }}</td>
                                <td>{{ $nt['so_dien_thoai'] ?? '-' }}</td>
                                <td>
                                    @if (!empty($nt['ngay_sinh']))
                                        {{ \Carbon\Carbon::parse($nt['ngay_sinh'])->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('chu-tro.nguoi-than.destroy', [$nt['id'], $khachThueId]) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn chắc chắn muốn xóa người này?')">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Chưa có người thân nào được thêm.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">⬅ Quay lại</a>
        </div>
    </div>
@endsection