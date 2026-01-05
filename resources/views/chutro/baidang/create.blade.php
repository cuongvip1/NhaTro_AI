@extends('layouts.chu-tro')

@section('title', 'Tạo bài đăng mới')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '🎉 Thành công!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#7c3aed'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: '⚠️ Lỗi!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#7c3aed'
            });
        </script>
    @endif

    <style>
        body {
            background: #f8f9ff;
            font-family: 'Inter', sans-serif;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i {
            color: #7c3aed;
        }

        .form-section {
            background: linear-gradient(180deg, #ffffff, #fbf9ff);
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(124, 58, 237, 0.06);
            border: 1px solid #ede9fe;
            padding: 28px 30px;
            margin-bottom: 24px;
            transition: 0.25s;
        }

        .form-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(124, 58, 237, 0.15);
        }

        .form-section h6 {
            font-weight: 700;
            color: #6d28d9;
            margin-bottom: 18px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label {
            font-weight: 600;
            color: #4b5563;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 10px 14px;
            font-size: .95rem;
            transition: 0.3s;
            background: #f9fafb;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8b5cf6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        }

        .tien-ich-box {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tien-ich-pill {
            background: #faf5ff;
            border: 1px solid #e9d5ff;
            border-radius: 999px;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #5b21b6;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .tien-ich-pill:hover {
            background: #ede9fe;
        }

        .tien-ich-pill input {
            accent-color: #7c3aed;
        }

        .add-tien-ich {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }

        .upload-zone {
            border: 2px dashed #c4b5fd;
            border-radius: 18px;
            background: #faf5ff;
            padding: 45px 20px;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
        }

        .upload-zone:hover {
            background: #f3e8ff;
            border-color: #8b5cf6;
        }

        .upload-zone i {
            font-size: 2.5rem;
            color: #7c3aed;
            margin-bottom: 8px;
        }

        .preview-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .preview-item img {
            width: 140px;
            height: 110px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            transition: 0.25s;
        }

        .btn-submit {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            border: none;
            color: white;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 22px;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            background: #5b21b6;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
            border-radius: 12px;
            padding: 10px 22px;
        }
    </style>

    <div class="container py-4">
        <div class="page-title">
            <i class="ri-home-smile-2-line"></i> Đăng bài cho thuê phòng
        </div>

        <form action="{{ route('chu-tro.bai-dang.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 🔹 Chọn phòng --}}
            <div class="form-section">
                <h6><i class="ri-door-line"></i> Chọn phòng có sẵn</h6>
                <select name="phong_id" id="phongSelect" class="form-select" required>
                    <option value="">-- Chọn phòng để đăng bài --</option>
                    @foreach ($phongList as $phong)
                        <option value="{{ $phong->id }}" data-dien-tich="{{ $phong->dien_tich }}" data-tang="{{ $phong->tang }}"
                            data-suc-chua="{{ $phong->suc_chua }}" data-trang-thai="{{ $phong->trang_thai }}"
                            data-day-tro="{{ $phong->dayTro->ten_day_tro }}">
                            {{ $phong->dayTro->ten_day_tro }} - Phòng {{ $phong->so_phong }} ({{ $phong->dien_tich }} m²)
                        </option>
                    @endforeach
                </select>

                <div id="phongInfo" class="mt-3 p-3 rounded-3 border bg-white shadow-sm" style="display:none;">
                    <h6 class="fw-bold text-purple-700 mb-2"><i class="ri-information-line"></i> Thông tin phòng</h6>
                    <ul class="mb-0" style="list-style:none; padding-left:0; color:#4b5563; font-size:0.95rem;">
                        <li><b>Dãy trọ:</b> <span id="phongDayTro"></span></li>
                        <li><b>Diện tích:</b> <span id="phongDienTich"></span> m²</li>
                        <li><b>Tầng:</b> <span id="phongTang"></span></li>
                        <li><b>Sức chứa:</b> <span id="phongSucChua"></span> người</li>
                        <li><b>Tình trạng:</b> <span id="phongTrangThai" class="badge bg-secondary"></span></li>
                    </ul>
                </div>
            </div>

            {{-- 🔹 Thông tin bài đăng --}}
            <div class="form-section">
                <h6><i class="ri-file-edit-line"></i> Thông tin bài đăng</h6>
                <div class="mb-3">
                    <label class="form-label">Tiêu đề bài đăng</label>
                    <input type="text" name="tieu_de" class="form-control"
                        placeholder="VD: Phòng đầy đủ nội thất, gần trung tâm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giá niêm yết (VNĐ / tháng)</label>
                    <input type="number" name="gia_niem_yet" class="form-control" placeholder="VD: 2500000" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ (nếu muốn thay đổi)</label>
                    <select name="dia_chi" class="form-select">
                        <option value="">-- Mặc định theo dãy trọ --</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->ten_dia_chi }}" {{ old('dia_chi') == $region->ten_dia_chi ? 'selected' : '' }}>{{ $region->ten_dia_chi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 🔹 Tiện ích --}}
            <div class="form-section">
                <h6><i class="ri-service-line"></i> Tiện ích đi kèm</h6>
                <div id="tienIchContainer" class="tien-ich-box">
                    @foreach ($tienIchList as $dv)
                        <label class="tien-ich-pill">
                            <input type="checkbox" name="tien_ich[]" value="{{ $dv->id }}">
                            {{ $dv->ten }} ({{ number_format($dv->don_gia, 0, ',', '.') }}/{{ $dv->don_vi }})
                        </label>
                    @endforeach
                </div>

                <div class="add-tien-ich">
                    <input type="text" id="newTienIch" class="form-control" placeholder="Nhập tiện ích mới...">
                    <button type="button" id="addTienIchBtn" class="btn btn-outline-primary"><i class="ri-add-line"></i>
                        Thêm</button>
                </div>
            </div>

            {{-- 🔹 Mô tả & Ảnh --}}
            <div class="form-section">
                <h6><i class="ri-pencil-line"></i> Mô tả & Ảnh phòng</h6>

                <textarea name="mo_ta" rows="4" class="form-control mb-3"
                    placeholder="Mô tả chi tiết về phòng, tiện ích, khu vực xung quanh..." required></textarea>

                <div id="uploadZone" class="upload-zone">
                    <i class="ri-upload-cloud-2-line"></i>
                    <p>Kéo & thả ảnh vào đây hoặc bấm để chọn ảnh từ máy</p>
                    <input type="file" name="anh[]" id="anhInput" multiple accept="image/*" hidden>
                    <button type="button" class="btn btn-outline-primary mt-2" id="chooseBtn">Chọn ảnh</button>
                </div>
                <div id="previewGrid" class="preview-grid"></div>
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-submit">Đăng bài</button>
                <a href="{{ route('chu-tro.bai-dang.index') }}" class="btn btn-cancel">Hủy</a>
            </div>
        </form>
    </div>

    <script>
        // 🔹 Thêm tiện ích mới
        document.getElementById('addTienIchBtn').addEventListener('click', () => {
            const input = document.getElementById('newTienIch');
            const val = input.value.trim();
            if (!val) return alert('Nhập tên tiện ích mới!');

            const div = document.createElement('label');
            div.classList.add('tien-ich-pill');
            const id = 'new_' + Date.now();
            div.innerHTML = `<input type="checkbox" name="tien_ich_moi[]" value="${val}" id="${id}" checked> ${val}`;
            document.getElementById('tienIchContainer').appendChild(div);
            input.value = '';
        });

        // 🔹 Upload & preview ảnh
        const uploadZone = document.getElementById('uploadZone');
        const anhInput = document.getElementById('anhInput');
        const previewGrid = document.getElementById('previewGrid');
        const chooseBtn = document.getElementById('chooseBtn');

        chooseBtn.onclick = () => anhInput.click();
        uploadZone.onclick = () => anhInput.click();

        uploadZone.addEventListener('dragover', e => {
            e.preventDefault();
            uploadZone.classList.add('hover');
        });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('hover'));
        uploadZone.addEventListener('drop', e => {
            e.preventDefault();
            uploadZone.classList.remove('hover');
            handleFiles(e.dataTransfer.files);
        });

        anhInput.addEventListener('change', e => handleFiles(e.target.files));

        function handleFiles(files) {
            previewGrid.innerHTML = ''; // reset khi chọn lại

            const maxFiles = 10; // giới hạn số ảnh
            if (files.length > maxFiles) {
                alert(`⚠️ Tối đa chỉ được chọn ${maxFiles} ảnh.`);
                return;
            }

            for (const file of files) {
                // ❌ Nếu không phải ảnh thì bỏ qua
                if (!file.type.startsWith('image/')) {
                    alert(`❌ ${file.name} không phải là ảnh hợp lệ.`);
                    continue;
                }

                // ✅ Không giới hạn dung lượng ảnh
                const reader = new FileReader();
                reader.onload = e => {
                    const wrap = document.createElement('div');
                    wrap.classList.add('preview-item');
                    wrap.innerHTML = `<img src="${e.target.result}" alt="preview">`;
                    previewGrid.appendChild(wrap);
                };
                reader.readAsDataURL(file);
            }
        }

        // 🔹 Hiển thị thông tin phòng khi chọn
        const phongSelect = document.getElementById('phongSelect');
        const phongInfo = document.getElementById('phongInfo');
        const fields = {
            dayTro: document.getElementById('phongDayTro'),
            dienTich: document.getElementById('phongDienTich'),
            tang: document.getElementById('phongTang'),
            sucChua: document.getElementById('phongSucChua'),
            trangThai: document.getElementById('phongTrangThai'),
        };

        phongSelect.addEventListener('change', function () {
            const opt = this.selectedOptions[0];
            if (!opt || !opt.value) {
                phongInfo.style.display = 'none';
                return;
            }
            fields.dayTro.textContent = opt.dataset.dayTro;
            fields.dienTich.textContent = opt.dataset.dienTich;
            fields.tang.textContent = opt.dataset.tang;
            fields.sucChua.textContent = opt.dataset.sucChua;
            fields.trangThai.textContent = opt.dataset.trangThai;
            phongInfo.style.display = 'block';
        });

        // 🔹 Validation trước khi gửi form
        document.querySelector('form').addEventListener('submit', function (e) {
            const hasChecked = document.querySelectorAll(
                'input[name="tien_ich[]"]:checked, input[name="tien_ich_moi[]"]:checked'
            ).length;

            if (hasChecked === 0) {
                e.preventDefault();
                alert('⚠️ Vui lòng chọn ít nhất một tiện ích!');
                return;
            }

            const files = document.getElementById('anhInput').files;
            if (files.length === 0) {
                e.preventDefault();
                alert('⚠️ Bạn chưa chọn ảnh nào để đăng bài!');
                return;
            }
        });
        // 🔹 Hiển thị loading khi submit form
        document.querySelector('form').addEventListener('submit', function (e) {
            Swal.fire({
                title: 'Đang đăng bài...',
                text: 'Vui lòng chờ trong giây lát.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        });

    </script>

@endsection