<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KhachThueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cccd' => fake()->unique()->numerify('0##############'),
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ];
    }
}