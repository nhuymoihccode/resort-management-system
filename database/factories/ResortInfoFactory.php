<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ResortInfoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Resort Test',
            'address' => fake('vi_VN')->address(),
            'phone' => fake('vi_VN')->phoneNumber(),
            'email' => fake()->safeEmail(),
        ];
    }
}