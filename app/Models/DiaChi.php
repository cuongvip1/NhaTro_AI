<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaChi extends Model
{
    protected $table = 'dia_chi';

    // Bảng hiện tại có thể không có cột created_at/updated_at,
    // nên tắt timestamps để Eloquent không cố chèn các cột này.
    public $timestamps = false;

    protected $fillable = [
        'ten_dia_chi',
    ];
}
