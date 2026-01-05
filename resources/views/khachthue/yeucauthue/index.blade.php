@extends('layouts.tenant-layout')

@section('title', 'Yêu cầu thuê phòng')
@section('page_title', 'Danh sách yêu cầu thuê')

@section('tenant_content')
 

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="ri-home-heart-line text-indigo-500 text-2xl"></i>
                Yêu cầu thuê của bạn
            </h2>
        </div>

        {{-- ⚡ Hiển thị thông báo --}}
        @if(session('success'))
            <div class="p-3 mb-4 bg-green-100 text-green-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 mb-4 bg-red-100 text-red-700 rounded-lg shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if(empty($yeuCauThue) || count($yeuCauThue) === 0)
            <div class="flex flex-col items-center justify-center h-56 text-gray-500 dark:text-gray-400">
                <i class="ri-file-warning-line text-4xl mb-3"></i>
                <p>Bạn chưa gửi yêu cầu thuê phòng nào.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($yeuCauThue as $yc)
                    @php
                        $baiDang = $yc['bai_dang'] ?? [];
                        $phong = $baiDang['phong'] ?? [];
                        $dayTro = $phong['day_tro'] ?? [];
                    @endphp

                    <div
                        class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                                🏠 {{ $baiDang['tieu_de'] ?? 'Phòng trọ' }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                📍 {{ $dayTro['dia_chi'] ?? 'Chưa có địa chỉ' }}
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                💰 {{ number_format($baiDang['gia_niem_yet'] ?? 0) }}đ / tháng
                            </p>

                            {{-- Trạng thái --}}
                            @php
                                $st = $yc['trang_thai'] ?? '';
                                $badgeClass = [
                                    'cho_duyet' => 'bg-yellow-100 text-yellow-700',
                                    'chap_nhan' => 'bg-blue-100 text-blue-700',
                                    'da_tao_hop_dong' => 'bg-green-100 text-green-700',
                                    'tu_choi' => 'bg-red-100 text-red-700',
                                    'chu_tro_huy_hop_dong' => 'bg-red-100 text-red-700',
                                    'huy' => 'bg-gray-200 text-gray-700',
                                ][$st] ?? 'bg-gray-200 text-gray-700';

                                $badgeText = [
                                    'cho_duyet' => 'Chờ duyệt',
                                    'chap_nhan' => 'Đã chấp nhận',
                                    'da_tao_hop_dong' => 'Đã tạo hợp đồng',
                                    'tu_choi' => 'Bị từ chối',
                                    'chu_tro_huy_hop_dong' => 'Chủ trọ hủy hợp đồng',
                                    'huy' => 'Bạn đã hủy',
                                ][$st] ?? 'Không rõ';
                            @endphp

                            <p class="text-sm mt-2">
                                ✍️ <strong>Trạng thái:</strong>
                                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $badgeClass }}">
                                    {{ $badgeText }}
                                </span>
                            </p>


                            {{-- Ghi chú --}}
                            @if(!empty($yc['ghi_chu']))
                                <p class="text-xs italic text-gray-500 mt-1">📝 {{ $yc['ghi_chu'] }}</p>
                            @endif

                            {{-- Ngày tạo --}}
                            <p class="text-xs text-gray-400 mt-3">
                                🕒 Gửi lúc: {{ \Carbon\Carbon::parse($yc['ngay_tao'])->format('d/m/Y H:i') ?? '--' }}
                            </p>
                            {{-- Nút hủy yêu cầu --}}
                            @if($yc['trang_thai'] === 'cho_duyet')
                                <form action="{{ route('khach-thue.yeu-cau-thue.huy', $yc['id']) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy yêu cầu thuê này không?')" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-red-500 hover:bg-red-600 text-white py-2 text-sm rounded-lg font-medium transition">
                                        <i class="ri-close-circle-line mr-1"></i> Hủy yêu cầu
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        @endif

@endsection