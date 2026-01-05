<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TienIch extends Model
{
    use HasFactory;

    protected $table = 'tien_ich'; 

    protected $fillable = ['ten']; 

    public $timestamps = false; 
    public function phongs()
    {
        return $this->belongsToMany(Phong::class, 'phong_tien_ich', 'tien_ich_id', 'phong_id');
    }
}
