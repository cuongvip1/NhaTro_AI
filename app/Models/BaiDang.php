<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiDang extends Model
{
    use HasFactory;

    protected $table = 'bai_dang';
    public $timestamps = false;
    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'nguoi_dung_id',
        'phong_id',
        'tieu_de',
        'mo_ta',
        'gia_niem_yet',
        'dia_chi',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
        'ngay_cap_nhat' => 'datetime',
    ];

    // ==========================
    // QUAN HỆ
    // ==========================
    public function anh()
    {

        return $this->hasMany(AnhBaiDang::class, 'bai_dang_id');
    }

    public function phong()
    {
        return $this->belongsTo(Phong::class, 'phong_id');
    }

    public function chuTro()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    // ==========================
    // ACCESSORS
    // ==========================
    public function getAnhDaiDienAttribute()
    {
        // ✅ dùng trong view: $post->anh_dai_dien
        $first = $this->anh->first();
        return $first ? asset('storage/' . $first->url) : asset('no-image.png');
    }

    public function getTrangThaiLabelAttribute()
    {
        return match ($this->trang_thai) {
            'cho_duyet' => 'Chờ duyệt',
            'dang' => 'Đang hiển thị',
            'an' => 'Đã ẩn',
            'tu_choi' => 'Từ chối',
            default => 'Không xác định',
        };
    }
}
