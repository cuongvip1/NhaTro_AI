<h2>❌ Yêu cầu thuê phòng bị từ chối</h2>

<p>Xin chào <b>{{ $khach->ho_ten }}</b>,</p>

<p>Rất tiếc, chủ trọ <b>{{ $chuTro->ho_ten ?? 'Chủ trọ' }}</b> đã từ chối yêu cầu thuê của bạn cho:</p>

<ul>
    <li>🏠 Phòng: <b>{{ $phong->ten_phong ?? '' }}</b></li>
    <li>🏘️ Dãy trọ: <b>{{ $phong->day_tro ?? '' }}</b></li>
</ul>

<p>Bạn có thể xem thêm các phòng khác tại trang chủ của hệ thống.</p>

<p>Trân trọng,<br><b>Hệ thống Nhà Trọ</b></p>