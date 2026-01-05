<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class NguoiDungFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ho_ten' => fake()->name(),
            'so_dien_thoai' => fake()->unique()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'mat_khau' => '123456',
            'vai_tro' => 'khach_thue',
            'trang_thai' => 'hoat_dong',
            'anh_dai_dien' => 'default.png',
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ];
    }
}