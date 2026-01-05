<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachThue extends Model
{
    use HasFactory;

    protected $table = 'khach_thue';
    public $timestamps = false;

    public function hopDong()
    {
        return $this->hasMany(HopDong::class, 'khach_thue_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
