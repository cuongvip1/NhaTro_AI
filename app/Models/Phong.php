<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    use HasFactory;

    protected $table = 'phong';
    protected $primaryKey = 'id';

    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'day_tro_id',
        'so_phong',
        'gia',
        'trang_thai',
        'suc_chua',
        'dien_tich',
        'tang',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
        'ngay_cap_nhat' => 'datetime',
    ];

    public function dayTro()
    {
        return $this->belongsTo(DayTro::class, 'day_tro_id', 'id');
    }
    public function dichVuDinhKy()
    {
        return $this->hasMany(DichVuDinhKy::class, 'phong_id', 'id')
            ->with('dichVu');
    }

    public function baiDang()
    {
        return $this->hasMany(BaiDang::class, 'phong_id', 'id');
    }
    public function hopDongs()
    {
        return $this->hasMany(HopDong::class, 'phong_id', 'id');
    }

    public function getDiaChiDayTroAttribute()
    {
        return $this->dayTro?->dia_chi ?? 'Chưa có địa chỉ';
    }

    public function getTenPhongDayTroAttribute()
    {
        $tenDay = $this->dayTro?->ten_day_tro ?? '';
        return trim("{$tenDay} - Phòng {$this->so_phong}");
    }
    public function tienIch()
    {
        return $this->belongsToMany(\App\Models\TienIch::class, 'phong_tien_ich', 'phong_id', 'tien_ich_id');
    }

}
