@component('mail::message')
# 📩 Yêu cầu thuê phòng mới

Xin chào **{{ $chuTro->ho_ten ?? 'Chủ trọ' }}**,

Khách thuê **{{ $khach->ho_ten ?? 'Không rõ' }}** vừa gửi yêu cầu thuê phòng.

**Thông tin chi tiết:**
- 🏠 Phòng: {{ $phong->so_phong ?? 'Không xác định' }}
- 🏘 Dãy trọ: {{ $dayTro->ten_day_tro ?? 'Không xác định' }}
- 💰 Tiền cọc: {{ number_format($phong->gia ?? 0) }} VNĐ
- 📅 Ngày gửi: {{ now()->format('d/m/Y H:i') }}

@component('mail::button', ['url' => $urlXemYeuCau])
Xem yêu cầu thuê
@endcomponent

Trân trọng,<br>
**Hệ thống Nhà Trọ**
@endcomponent