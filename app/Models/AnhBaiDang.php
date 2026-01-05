<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnhBaiDang extends Model
{
    use HasFactory;

    protected $table = 'anh_bai_dang';
    public $timestamps = false;

    protected $fillable = [
        'bai_dang_id',
        'url',
        'thu_tu',
        'ngay_tao',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
    ];

    public function baiDang()
    {
        return $this->belongsTo(BaiDang::class, 'bai_dang_id');
    }

    public function getUrlAttribute($value)
    {
        if (!$value)
            return null;

        $value = str_replace(['public/', 'storage/'], '', $value);

        $apiBase = rtrim(env('API_BASE_URL', config('app.url')), '/');
        return "{$apiBase}/storage/" . ltrim($value, '/');
    }

}
