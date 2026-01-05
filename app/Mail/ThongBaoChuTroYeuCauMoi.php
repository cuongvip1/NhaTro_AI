
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThongBaoChuTroYeuCauMoi extends Mailable
{
use Queueable, SerializesModels;

public $chuTro, $khach, $phong, $dayTro, $urlXemYeuCau;

public function __construct($chuTro, $khach, $phong, $dayTro = null)
{
$this->chuTro = $chuTro;
$this->khach = $khach;
$this->phong = $phong;
$this->dayTro = $dayTro;
$this->urlXemYeuCau = url('/chu-tro/yeu-cau-thue');
}

public function build()
{
$chuTro = e($this->chuTro->ho_ten ?? 'Chủ trọ');
$khach = e($this->khach->ho_ten ?? 'Không rõ');
$phong = e($this->phong->so_phong ?? 'Không xác định');
$dayTro = e($this->dayTro->ten_day_tro ?? 'Không xác định');
$gia = number_format($this->phong->gia ?? 0);
$url = e($this->urlXemYeuCau);
$time = now()->format('d/m/Y H:i');

$html = <<<HTML <div style="font-family:Arial,sans-serif;background:#f9fafb;padding:20px;color:#333;">
    <div
        style="max-width:600px;margin:auto;background:#fff;padding:20px 30px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color:#6d28d9;">📩 Yêu cầu thuê phòng mới</h2>
        <p>Xin chào <b>{$chuTro}</b>,</p>
        <p>Khách thuê <b>{$khach}</b> vừa gửi yêu cầu thuê:</p>
        <ul>
            <li>🏠 Phòng: <b>{$phong}</b></li>
            <li>🏘 Dãy trọ: <b>{$dayTro}</b></li>
            <li>💰 Tiền cọc: <b>{$gia} VNĐ</b></li>
            <li>⏰ Thời gian gửi: {$time}</li>
        </ul>
        <p style="margin-top:20px;">
            <a href="{$url}"
                style="background:#6d28d9;color:#fff;padding:12px 18px;text-decoration:none;border-radius:8px;font-weight:bold;">
                🔍 Xem yêu cầu thuê
            </a>
        </p>
        <hr style="margin:25px 0;">
        <p style="font-size:14px;color:#666;">
            Email tự động từ hệ thống Nhà Trọ.<br>
            📅 Gửi lúc: {$time}
        </p>
    </div>
    </div>
    HTML;

    return $this->subject('📩 Có yêu cầu thuê phòng mới')->html($html);
    }
    }