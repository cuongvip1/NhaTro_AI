@extends('layouts.app')

@section('title', $item['tieu_de'] ?? 'Chi tiết phòng trọ')

@section('content')
    <section class="py-10 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-6xl mx-auto px-4">

            {{-- 🏠 Card tổng --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">

                {{-- Gallery kiểu Shopee --}}
                <div class="md:w-[60%] p-6 flex flex-col items-center">
                    @if(!empty($item['anh']) && is_array($item['anh']) && count($item['anh']) > 0)
                        {{-- 🖼 Ảnh lớn có kính lúp và popup zoom --}}
                        <div class="relative flex flex-col items-center">
                            {{-- Ảnh lớn hiển thị --}}
                            <div id="imageContainer" class="relative overflow-hidden rounded-xl shadow w-full max-w-[600px]">
                                <img id="mainImage" src="{{ $item['anh'][0] ?? asset('images/no-image.png') }}"
                                    class="w-full h-[500px] object-cover rounded-xl cursor-crosshair select-none transition-transform duration-300 hover:scale-[1.02]"
                                    alt="Ảnh phòng">

                                {{-- Kính lúp nhỏ trên ảnh --}}
                                <div id="lens"
                                    class="hidden absolute w-32 h-32 border-2 border-purple-400 rounded-full bg-white/20 backdrop-blur-sm pointer-events-none transition-all duration-200">
                                </div>
                            </div>
                        </div>

                        {{-- Popup phóng to giữa màn hình --}}
                        <div id="zoomPopup"
                            class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity duration-300">
                            <div
                                class="relative bg-white dark:bg-gray-900 p-4 rounded-2xl shadow-2xl max-w-[90vw] max-h-[90vh] flex items-center justify-center">
                                <img id="zoomImage" src=""
                                    class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-lg border-4 border-purple-500"
                                    alt="Zoom ảnh">
                                <button onclick="closeZoomPopup()"
                                    class="absolute top-3 right-3 bg-purple-600 hover:bg-purple-700 text-white rounded-full p-2 transition">
                                    <i class="ri-close-line text-xl"></i>
                                </button>
                            </div>
                        </div>


                        {{-- Ảnh nhỏ (thumbnail hàng ngang, luôn hiện) --}}
                        @php
                            $thumbs = count($item['anh']) > 1 ? $item['anh'] : array_fill(0, 4, $item['anh'][0]);
                        @endphp
                        <div class="flex flex-wrap gap-3 justify-center">
                            @foreach($thumbs as $url)
                                <img src="{{ $url }}" onclick="changeMainImage('{{ $url }}', this)"
                                    class="thumb h-24 w-24 object-cover rounded-xl border-2 border-transparent cursor-pointer transition-all duration-300 hover:border-purple-500 hover:scale-105"
                                    alt="Ảnh phụ">
                            @endforeach
                        </div>
                    @else
                        <img src="{{ asset('images/no-image.png') }}" class="w-full h-[500px] object-cover rounded-xl shadow"
                            alt="Không có ảnh">
                    @endif
                </div>

                {{-- 📋 Thông tin bên phải --}}
                <div class="md:w-[40%] p-8 flex flex-col justify-between">
                    <div>
                        {{-- 💰 Giá + Trạng thái --}}
                        <div class="flex justify-between items-start mb-5">
                            <h2 class="text-3xl font-extrabold text-purple-600">
                                {{ $item['gia_hien_thi'] ?? '--' }}
                                <span class="text-gray-600 text-lg font-medium"></span>
                            </h2>

                            @if(!empty($item['trang_thai_phong']))
                                <span
                                    class="px-4 py-1 rounded-full text-sm font-semibold
                                                                                                                                                                                        @if($item['trang_thai_phong'] === 'trong')
                                                                                                                                                                                            bg-green-100 text-green-700
                                                                                                                                                                                        @elseif($item['trang_thai_phong'] === 'da_thue')
                                                                                                                                                                                            bg-red-100 text-red-700
                                                                                                                                                                                        @else
                                                                                                                                                                                            bg-gray-100 text-gray-500
                                                                                                                                                                                        @endif">
                                    {{ $item['trang_thai_phong'] === 'trong' ? 'Trống' : ($item['trang_thai_phong'] === 'da_thue' ? 'Đã thuê' : 'Không khả dụng') }}
                                </span>
                            @endif
                        </div>

                        {{-- 🏠 Thông tin chi tiết --}}
                        <ul class="text-gray-700 dark:text-gray-300 space-y-2 mb-6">
                            <li><i class="ri-building-line text-purple-600 mr-2"></i>Dãy:
                                <b>{{ $item['ten_day_tro'] ?? '--' }}</b>
                            </li>
                            <li><i class="ri-home-3-line text-purple-600 mr-2"></i>Phòng:
                                <b>{{ $item['so_phong'] ?? '--' }}</b>
                            </li>
                            <li><i class="ri-ruler-line text-purple-600 mr-2"></i>Diện tích:
                                <b>{{ $item['dien_tich'] ?? '--' }} m²</b>
                            </li>
                            <li><i class="ri-building-2-line text-purple-600 mr-2"></i>Tầng:
                                <b>{{ $item['tang'] ?? '--' }}</b>
                            </li>
                            <li><i class="ri-team-line text-purple-600 mr-2"></i>Sức chứa:
                                <b>{{ $item['suc_chua'] ?? '--' }} người</b>
                            </li>
                            <li><i class="ri-calendar-line text-purple-600 mr-2"></i>Ngày đăng:
                                <b>{{ $item['ngay_hien_thi'] ?? '--' }}</b>
                            </li>
                        </ul>

                        {{-- ⭐ Đánh giá trung bình --}}
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Đánh giá trung bình</h3>
                            @if(!empty($item['rating']) && $item['rating'] > 0)
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($item['rating']))
                                            <i class="ri-star-fill text-yellow-400 text-xl"></i>
                                        @elseif($i - $item['rating'] < 1)
                                            <i class="ri-star-half-fill text-yellow-400 text-xl"></i>
                                        @else
                                            <i class="ri-star-line text-gray-400 text-xl"></i>
                                        @endif
                                    @endfor
                                    <span
                                        class="ml-2 text-gray-700 dark:text-gray-300 font-medium">{{ number_format($item['rating'], 1) }}/5</span>
                                </div>
                            @else
                                <p class="text-gray-500 italic">Chưa có đánh giá</p>
                            @endif
                        </div>

                        {{-- 💰 Dịch vụ kèm theo --}}
                        <div class="mb-5">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Dịch vụ kèm theo</h3>
                            <table
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden text-center">
                                <thead class="bg-purple-100 dark:bg-purple-800 text-gray-700 dark:text-gray-100">
                                    <tr>
                                        <th class="py-2 px-3">Dịch vụ</th>
                                        <th class="py-2 px-3">Đơn giá</th>
                                        <th class="py-2 px-3">Đơn vị</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($item['dich_vu']))
                                        @foreach($item['dich_vu'] as $dv)
                                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                                <td class="py-2 px-3">{{ $dv['ten'] }}</td>
                                                <td class="py-2 px-3">{{ number_format($dv['gia'], 0, ',', '.') }}đ</td>
                                                <td class="py-2 px-3">{{ $dv['don_vi'] }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center text-gray-500 py-3">Chưa có dịch vụ</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="mt-6 flex gap-4">
                        @if($item['trang_thai_phong'] === 'trong')
                            <button type="button" onclick="showYeuCauThuePopup({{ $item['id'] }})"
                                class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-lg font-semibold transition">
                                <i class="ri-shopping-bag-line mr-2"></i>Gửi yêu cầu thuê
                            </button>
                        @else
                            <button class="flex-1 bg-gray-400 text-white py-2.5 rounded-lg font-semibold cursor-not-allowed">
                                <i class="ri-lock-line mr-2"></i>Không khả dụng
                            </button>
                        @endif

                        {{-- Nút yêu thích --}}
                        <button id="btn-yeu-thich" data-favorite="false"
                            class="flex-1 bg-pink-500 hover:bg-pink-600 text-white py-2.5 rounded-lg font-semibold transition flex items-center justify-center"
                            onclick="toggleYeuThich({{ $item['id'] }})">
                            <i id="iconYeuThich" class="ri-heart-3-line mr-2"></i>
                            <span id="textYeuThich">Thêm vào yêu thích</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 📝 Mô tả chi tiết --}}
            <div class="mt-10 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">Mô tả chi tiết</h2>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                    {{ $item['mo_ta'] ?? 'Chưa có mô tả chi tiết cho phòng này.' }}
                </p>
            </div>

            {{-- Đánh giá chi tiết --}}
            @if(!empty($item['danh_gia']))
                <div class="mt-10 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="ri-message-3-line text-purple-600 mr-2"></i> Đánh giá của người thuê
                    </h2>
                    @foreach($item['danh_gia'] as $dg)
                        <div class="border-b border-gray-100 dark:border-gray-700 py-4">
                            <div class="flex justify-between items-center">
                                <span
                                    class="font-semibold text-gray-800 dark:text-white">{{ $dg['nguoi_danh_gia'] ?? 'Ẩn danh' }}</span>
                                <span
                                    class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($dg['ngay_tao'])->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $dg['diem_so'])
                                        <i class="ri-star-fill text-yellow-400"></i>
                                    @else
                                        <i class="ri-star-line text-gray-400"></i>
                                    @endif
                                @endfor
                                <span
                                    class="ml-2 text-gray-700 dark:text-gray-300 font-medium">{{ number_format($dg['diem_so'], 1) }}/5</span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 mt-1 italic">“{{ $dg['binh_luan'] ?? '' }}”</p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Nút quay lại --}}
            <div class="mt-8 text-right">
                <a href="{{ route('listing') }}"
                    class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">
                    <i class="ri-arrow-left-line mr-2"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let activeThumb = null;
        function changeMainImage(url, el) {
            const mainImage = document.getElementById('mainImage');
            mainImage.classList.add('fade');
            setTimeout(() => {
                mainImage.src = url;
                mainImage.classList.remove('fade');
            }, 150);

            if (activeThumb) activeThumb.classList.remove('active');
            el.classList.add('active');
            activeThumb = el;
        }
    </script>
    <script>
        const imageContainer = document.getElementById("imageContainer");
        const mainImage = document.getElementById("mainImage");
        const lens = document.getElementById("lens");
        const zoomPopup = document.getElementById("zoomPopup");
        const zoomImage = document.getElementById("zoomImage");
        imageContainer.addEventListener("mouseenter", () => {
            lens.classList.remove("hidden");
        });

        imageContainer.addEventListener("mouseleave", () => {
            lens.classList.add("hidden");
        });

        imageContainer.addEventListener("mousemove", (e) => {
            const rect = mainImage.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const half = lens.offsetWidth / 2;

            let left = x - half;
            let top = y - half;

            left = Math.max(0, Math.min(left, rect.width - lens.offsetWidth));
            top = Math.max(0, Math.min(top, rect.height - lens.offsetHeight));

            lens.style.left = `${left}px`;
            lens.style.top = `${top}px`;
        });

        mainImage.addEventListener("click", () => {
            zoomImage.src = mainImage.src;
            zoomPopup.classList.remove("hidden");
            zoomPopup.classList.add("flex");
            setTimeout(() => zoomPopup.classList.add("opacity-100"), 10);
        });

        function closeZoomPopup() {
            zoomPopup.classList.add("hidden");
            zoomPopup.classList.remove("opacity-100");
        }

        zoomPopup.addEventListener("click", (e) => {
            if (e.target === zoomPopup) closeZoomPopup();
        });
    </script>
    {{-- -
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    <script>
        async function toggleYeuThich(baiDangId) {
            const token = localStorage.getItem('token');

            if (!token) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cần đăng nhập',
                    text: 'Vui lòng đăng nhập với tư cách khách thuê để thêm vào yêu thích.',
                    confirmButtonText: 'Đăng nhập ngay',
                    confirmButtonColor: '#8B5CF6'
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '/login';
                    }
                });
                return;
            }

            const btn = document.getElementById('btn-yeu-thich');
            const icon = document.getElementById('iconYeuThich');
            const text = document.getElementById('textYeuThich');
            const isFavorite = btn.dataset.favorite === 'true';

            try {
                const url = `${window.API_URL}/khach-thue/yeu-thich/${baiDangId}`;
                const method = isFavorite ? 'DELETE' : 'POST';

                const res = await fetch(url, {
                    method,
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                let data;
                try {
                    data = await res.json();
                } catch {
                    data = { message: 'Máy chủ trả về phản hồi không hợp lệ.' };
                }

                if (!res.ok) {
                    if (res.status === 401) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Hình như bạn chưa đăng nhập',
                            html: `
                                                                                                    <p style="font-size: 15px; margin-top: 6px;">
                                                                                                        Vui lòng đăng nhập lại để tiếp tục sử dụng hệ thống nhé 💫
                                                                                                    </p>
                                                                                                `,
                            confirmButtonText: 'Đăng nhập lại',
                            confirmButtonColor: '#4f46e5',
                            background: '#ffffff',
                            color: '#1e293b',
                            iconColor: '#4f46e5',
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp'
                            },
                            customClass: {
                                confirmButton: 'rounded-lg px-5 py-2.5 text-sm font-medium shadow-md'
                            }
                        }).then(() => {
                            window.location.href = '/login';
                        });
                        return;
                    }

                    if (res.status === 403) {
                        Swal.fire('Từ chối truy cập', 'Chỉ khách thuê mới được sử dụng chức năng này.', 'error');
                        return;
                    }

                    throw new Error(data.message || 'Lỗi không xác định.');
                }

                if (isFavorite) {
                    btn.dataset.favorite = 'false';
                    icon.className = 'ri-heart-3-line mr-2';
                    text.textContent = 'Thêm vào yêu thích';
                    btn.classList.remove('bg-red-500');
                    btn.classList.add('bg-pink-500');
                } else {
                    btn.dataset.favorite = 'true';
                    icon.className = 'ri-heart-fill mr-2 text-yellow-200';
                    text.textContent = 'Đã yêu thích';
                    btn.classList.remove('bg-pink-500');
                    btn.classList.add('bg-red-500');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: data.message || 'Đã cập nhật danh sách yêu thích.',
                    timer: 1800,
                    showConfirmButton: false
                });

            } catch (error) {
                console.error('Lỗi toggle yêu thích:', error);
                Swal.fire('Lỗi', error.message, 'error');
            }
        }
    </script>
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#8B5CF6'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#EF4444'
            });
        </script>
    @endif

    <script>
        async function showYeuCauThuePopup(baiDangId) {
            const token = localStorage.getItem('token');
            if (!token) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Bạn chưa đăng nhập',
                    text: 'Vui lòng đăng nhập để gửi yêu cầu thuê phòng.',
                    confirmButtonText: 'Đăng nhập ngay',
                    confirmButtonColor: '#8B5CF6'
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '/login';
                    }
                });
                return;
            }
            const htmlForm = `
                                                <style>
                                                    .swal2-popup {
                                                        width: 750px !important;
                                                        padding: 25px 30px !important;
                                                        font-size: 16px !important;
                                                        border-radius: 18px !important;
                                                    }
                                                    .swal2-input, .swal2-textarea, .swal2-file {
                                                        width: 100% !important;
                                                        margin: 6px 0 15px 0 !important;
                                                        padding: 10px 12px !important;
                                                        border: 1px solid #ccc !important;
                                                        border-radius: 10px !important;
                                                        font-size: 15px !important;
                                                    }
                                                    .swal2-textarea { resize: none; }
                                                    .swal2-label {
                                                        font-weight: 600;
                                                        color: #333;
                                                        display: block;
                                                        margin-bottom: 4px;
                                                    }
                                                    #filePreview {
                                                        font-size: 14px;
                                                        color: #6b21a8;
                                                        font-weight: 500;
                                                    }
                                                    .swal2-confirm {
                                                        background: linear-gradient(90deg, #8B5CF6, #6D28D9) !important;
                                                        font-weight: 600 !important;
                                                        border-radius: 10px !important;
                                                        padding: 10px 20px !important;
                                                    }
                                                    .swal2-cancel {
                                                        border-radius: 10px !important;
                                                        font-weight: 500 !important;
                                                        padding: 10px 20px !important;
                                                    }
                                                    .hopdong-box {
                                                        background: #f9f5ff;
                                                        border: 1px solid #d8b4fe;
                                                        padding: 12px 15px;
                                                        border-radius: 10px;
                                                        margin-bottom: 18px;
                                                    }
                                                    .btn-view {
                                                        background: #8B5CF6;
                                                        color: white;
                                                        border: none;
                                                        border-radius: 8px;
                                                        padding: 6px 14px;
                                                        cursor: pointer;
                                                        font-weight: 500;
                                                        transition: all 0.2s;
                                                    }
                                                    .btn-view:hover { background: #6D28D9; }
                                                </style>

                                                <form id="yeuCauForm" class="text-left">
                                                    <label class="swal2-label">Số CCCD (bắt buộc):</label>
                                                    <input type="text" id="cccd" class="swal2-input" placeholder="VD: 012345678912" maxlength="20" required>

                                                    <label class="swal2-label">Số điện thoại của bạn (bắt buộc):</label>
                                                    <input type="text" id="soDienThoai" class="swal2-input" placeholder="VD: 0901234567" maxlength="15" required>

                                                    <label class="swal2-label">Ngày bắt đầu thuê:</label>
                                                    <input type="date" id="ngayBatDau" class="swal2-input" required>

                                                    <label class="swal2-label">Ngày kết thúc:</label>
                                                    <input type="date" id="ngayKetThuc" class="swal2-input" required>

                                                    <label class="swal2-label">Tiền cọc (VNĐ):</label>
                                                    <input type="number" id="tienCoc" class="swal2-input" placeholder="VD: 1000000" min="0" required>

                                                    <label class="swal2-label">Ghi chú:</label>
                                                    <textarea id="ghiChu" class="swal2-textarea" placeholder="VD: Em muốn dọn vào đầu tháng tới..." rows="3"></textarea>

                                                    <div class="hopdong-box">
                                                        <label class="swal2-label">📄 Xem hợp đồng mẫu:</label>
                                                        <p style="font-size: 14px; color: #555;">Vui lòng đọc kỹ các điều khoản trước khi gửi yêu cầu.</p>
                                                        <button type="button" id="btnXemHopDongMau" class="btn-view">
                                                            Xem hợp đồng mẫu
                                                        </button>
                                                    </div>

                                                    <label class="swal2-label">📤 Tải lên file hợp đồng của bạn (PDF):</label>
                                                    <input type="file" id="fileHopDong" class="swal2-file" accept=".pdf" required>
                                                    <div id="filePreview"></div>

                                                    <label class="swal2-label">Người thân sống cùng (tùy chọn):</label>
                                                    <input type="text" id="tenNguoiThan" class="swal2-input" placeholder="Họ tên người thân">
                                                    <input type="text" id="sdtNguoiThan" class="swal2-input" placeholder="Số điện thoại người thân">
                                                    <input type="text" id="moiQuanHe" class="swal2-input" placeholder="Mối quan hệ (VD: anh, bạn,...)">
                                                </form>
                                            `;

            const { value: confirm } = await Swal.fire({
                title: '📋 Gửi yêu cầu thuê phòng',
                html: htmlForm,
                focusConfirm: false,
                confirmButtonText: 'Gửi yêu cầu',
                confirmButtonColor: '#8B5CF6',
                cancelButtonText: 'Hủy',
                showCancelButton: true,
                didOpen: () => {
                    document.getElementById('btnXemHopDongMau').addEventListener('click', () => {
                        const fileUrl = '{{ asset("files/hopdong_mau.pdf") }}';
                        window.open(fileUrl, '_blank');
                    });

                    const fileInput = document.getElementById('fileHopDong');
                    const preview = document.getElementById('filePreview');
                    fileInput.addEventListener('change', (e) => {
                        const file = e.target.files[0];
                        preview.textContent = file ? `📎 ${file.name}` : '';
                    });
                },
                preConfirm: () => {
                    const cccd = document.getElementById('cccd').value.trim();
                    const sdt = document.getElementById('soDienThoai').value.trim();
                    const ngayBatDau = document.getElementById('ngayBatDau').value;
                    const ngayKetThuc = document.getElementById('ngayKetThuc').value;
                    const tienCoc = document.getElementById('tienCoc').value;
                    const file = document.getElementById('fileHopDong').files[0];
                    const tenNguoiThan = document.getElementById('tenNguoiThan').value.trim();
                    const sdtNguoiThan = document.getElementById('sdtNguoiThan').value.trim();
                    const moiQuanHe = document.getElementById('moiQuanHe').value.trim();
                    const ghiChu = document.getElementById('ghiChu').value.trim();

                    if (!cccd || !sdt || !ngayBatDau || !ngayKetThuc || !tienCoc || !file) {
                        Swal.showValidationMessage('⚠️ Vui lòng nhập đầy đủ thông tin bắt buộc.');
                        return false;
                    }

                    if (!/^[0-9]{9,12}$/.test(cccd)) {
                        Swal.showValidationMessage('⚠️ Số CCCD không hợp lệ (chỉ chứa 9-12 chữ số).');
                        return false;
                    }

                    if (!/^0\d{9,10}$/.test(sdt)) {
                        Swal.showValidationMessage('⚠️ Số điện thoại không hợp lệ (phải bắt đầu bằng 0 và có 10-11 số).');
                        return false;
                    }


                    const nguoi_than = (tenNguoiThan || sdtNguoiThan || moiQuanHe)
                        ? [{ ho_ten: tenNguoiThan, so_dien_thoai: sdtNguoiThan, moi_quan_he: moiQuanHe }]
                        : [];

                    return {
                        cccd,
                        so_dien_thoai: sdt,
                        ngay_bat_dau: ngayBatDau,
                        ngay_ket_thuc: ngayKetThuc,
                        tien_coc: tienCoc,
                        ghi_chu: ghiChu,
                        nguoi_than,
                        file
                    };
                }
            });

            if (!confirm) return;

            /*const token = localStorage.getItem('token');
            if (!token) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Bạn chưa đăng nhập',
                    text: 'Vui lòng đăng nhập để gửi yêu cầu thuê.',
                    confirmButtonText: 'Đăng nhập ngay',
                    confirmButtonColor: '#8B5CF6'
                }).then(() => window.location.href = '/login');
                return;
            }*/

            const formData = new FormData();
            formData.append('bai_dang_id', baiDangId);
            formData.append('cccd', confirm.cccd);
            formData.append('so_dien_thoai', confirm.so_dien_thoai);
            formData.append('ngay_bat_dau', confirm.ngay_bat_dau);
            formData.append('ngay_ket_thuc', confirm.ngay_ket_thuc);
            formData.append('tien_coc', confirm.tien_coc);
            formData.append('ghi_chu', confirm.ghi_chu);
            formData.append('file_hop_dong', confirm.file);
            formData.append('nguoi_than', JSON.stringify(confirm.nguoi_than));

            Swal.fire({
                title: 'Đang gửi yêu cầu...',
                text: 'Vui lòng chờ trong giây lát ⏳',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch(`${window.API_URL}/khach-thue/yeu-cau-thue`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` },
                    body: formData
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Gửi yêu cầu thất bại.');

                Swal.fire({
                    icon: 'success',
                    title: 'Thành công 🎉',
                    text: data.message || 'Yêu cầu thuê đã được gửi!',
                    confirmButtonColor: '#8B5CF6'
                }).then(() => window.location.reload());
            } catch (e) {
                Swal.fire('Lỗi', e.message, 'error');
            }
        }
    </script>


@endsection