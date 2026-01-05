<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DichVu extends Model
{
    use HasFactory;

    protected $table = 'dich_vu';
    protected $primaryKey = 'id';

    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'ma',
        'ten',
        'don_vi',
        'don_gia',
        'co_dong_ho',
        'ngay_tao',
        'ngay_cap_nhat',
    ];

    /**
     * 🔗 Một dịch vụ có thể được dùng trong nhiều bản ghi định kỳ
     */
    public function dichVuDinhKy()
    {
        return $this->hasMany(DichVuDinhKy::class, 'dich_vu_id', 'id');
    }

    /**
     * 🔗 Một dịch vụ có thể gắn vào nhiều đồng hồ (ví dụ: điện, nước)
     */
    public function dongHo()
    {
        return $this->hasMany(DongHo::class, 'dich_vu_id', 'id');
    }
}
