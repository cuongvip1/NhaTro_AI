<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChuTroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dia_chi' => fake()->address(),
        ];
    }
}