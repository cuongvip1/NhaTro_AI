@extends('layouts.chu-tro')

@section('title', 'Chỉnh sửa bài đăng')

@section('content')
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
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(124, 58, 237, 0.06);
            border: 1px solid #ede9fe;
            padding: 28px 30px;
            margin-bottom: 24px;
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

        .form-control {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 10px 14px;
            font-size: .95rem;
            background: #f9fafb;
            transition: .3s;
        }

        .form-control:focus {
            border-color: #8b5cf6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, .15);
        }

        .preview-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .preview-item {
            position: relative;
        }

        .preview-item img {
            width: 140px;
            height: 110px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .remove-btn {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
        }

        .upload-zone {
            border: 2px dashed #c4b5fd;
            border-radius: 18px;
            background: #faf5ff;
            padding: 35px 20px;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
        }

        .upload-zone:hover {
            background: #f3e8ff;
            border-color: #8b5cf6;
        }

        .btn-submit {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            border: none;
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
            <i class="ri-edit-box-line"></i> Chỉnh sửa bài đăng
        </div>

        <form action="{{ route('chu-tro.bai-dang.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Thông tin bài đăng --}}
            <div class="form-section">
                <h6><i class="ri-file-edit-line"></i> Thông tin</h6>
                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de', $post->tieu_de) }}"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá niêm yết (VNĐ / tháng)</label>
                    <input type="number" name="gia_niem_yet" class="form-control"
                        value="{{ old('gia_niem_yet', $post->gia_niem_yet) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả chi tiết</label>
                    <textarea name="mo_ta" rows="5" class="form-control"
                        required>{{ old('mo_ta', $post->mo_ta) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <select name="dia_chi" class="form-control">
                        <option value="">-- Mặc định theo dãy trọ --</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->ten_dia_chi }}" {{ (old('dia_chi', $post->dia_chi) == $region->ten_dia_chi) ? 'selected' : '' }}>{{ $region->ten_dia_chi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Ảnh cũ --}}
            <div class="form-section">
                <h6><i class="ri-image-line"></i> Ảnh hiện tại</h6>
                <div class="preview-grid" id="currentImages">
                    @foreach ($post->anh as $img)
                        <div class="preview-item">
                            <img src="{{ $img->url }}" alt="">
                            <button type="button" class="remove-btn" data-id="{{ $img->id }}">×</button>
                            <input type="hidden" name="keep_anh_cu[]" value="{{ $img->id }}">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Ảnh mới --}}
            <div class="form-section">
                <h6><i class="ri-upload-cloud-2-line"></i> Thêm ảnh mới</h6>
                <div id="uploadZone" class="upload-zone">
                    <p>Kéo & thả ảnh hoặc bấm chọn</p>
                    <input type="file" name="anh[]" id="anhInput" multiple accept="image/*" hidden>
                    <button type="button" id="chooseBtn" class="btn btn-outline-primary mt-2">Chọn ảnh</button>
                </div>
                <div id="previewGrid" class="preview-grid"></div>
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-submit">Lưu thay đổi</button>
                <a href="{{ route('chu-tro.bai-dang.show', $post->id) }}" class="btn btn-cancel">Hủy</a>
            </div>
        </form>
    </div>

    <script>
        const uploadZone = document.getElementById('uploadZone');
        const anhInput = document.getElementById('anhInput');
        const previewGrid = document.getElementById('previewGrid');
        const chooseBtn = document.getElementById('chooseBtn');

        chooseBtn.onclick = () => anhInput.click();
        uploadZone.onclick = () => anhInput.click();

        uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('hover'); });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('hover'));
        uploadZone.addEventListener('drop', e => {
            e.preventDefault();
            handleFiles(e.dataTransfer.files);
        });

        anhInput.addEventListener('change', e => handleFiles(e.target.files));

        function handleFiles(files) {
            previewGrid.innerHTML = '';
            for (const file of files) {
                if (!file.type.startsWith('image/')) continue;
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

        // Xóa ảnh cũ
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                this.closest('.preview-item').remove();
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'xoa_anh_cu[]';
                input.value = id;
                document.querySelector('form').appendChild(input);
            });
        });
    </script>
@endsection