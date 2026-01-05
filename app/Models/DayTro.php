<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayTro extends Model
{
    use HasFactory;

    protected $table = 'day_tro';
    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'chu_tro_id',
        'ten_day_tro',
        'dia_chi',
        'so_phong',
        'so_phong_trong',
        'so_phong_da_thue',
        'so_phong_bao_tri',
        'dien_tich_tb',
        'gia_trung_binh',
        'mo_ta',
        'tien_ich',
        'ngay_tao',
        'ngay_cap_nhat',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
        'ngay_cap_nhat' => 'datetime',
    ];

    // ==========================
    // 🔗 Quan hệ
    // ==========================
    /** 🔹 Dãy trọ thuộc về một chủ trọ */
    public function chuTro()
    {
        return $this->belongsTo(NguoiDung::class, 'chu_tro_id');
    }

    /** 🔹 Dãy trọ có nhiều phòng */
    public function phong()
    {
        return $this->hasMany(Phong::class, 'day_tro_id', 'id');
    }

    /** 🔹 Dãy trọ có nhiều bài đăng (thông qua phòng) */
    public function baiDang()
    {
        return $this->hasManyThrough(
            BaiDang::class,
            Phong::class,
            'day_tro_id',  // foreign key on phong
            'phong_id',    // foreign key on bai_dang
            'id',          // local key on day_tro
            'id'           // local key on phong
        );
    }

    // ==========================
    // 💡 Accessors
    // ==========================

    /** ✅ Gộp thông tin hiển thị đẹp */
    public function getThongTinDayTroAttribute()
    {
        return "{$this->ten_day_tro} - {$this->dia_chi}";
    }

    /** ✅ Lấy số lượng phòng khả dụng */
    public function getSoPhongTrongLabelAttribute()
    {
        return "{$this->so_phong_trong}/{$this->so_phong} phòng trống";
    }
}
