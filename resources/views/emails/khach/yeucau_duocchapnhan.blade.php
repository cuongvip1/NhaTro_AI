<h2>✅ Yêu cầu thuê phòng được chấp nhận</h2>
<p>Xin chào <b>{{ $khach->ho_ten }}</b>,</p>
<p>Chủ trọ <b>{{ $chuTro->ho_ten }}</b> đã chấp nhận yêu cầu thuê phòng của bạn.</p>
<ul>
    <li>🏠 Phòng: {{ $phong->so_phong }}</li>
    <li>🏘 Dãy trọ: {{ $dayTro->ten_day_tro }}</li>
    <li>📅 Ngày xác nhận: {{ now()->format('d/m/Y H:i') }}</li>
</ul>
<p>Cảm ơn bạn đã tin tưởng hệ thống Nhà Trọ 💜</p>