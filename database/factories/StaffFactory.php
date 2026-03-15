<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    public function definition(): array
    {
        return [
            'resort_info_id' => 1,
            'name' => fake('vi_VN')->name(),
            'position' => fake()->randomElement(['Lễ tân', 'Buồng phòng', 'Bảo vệ', 'Đầu bếp', 'Quản lý']),
            'salary' => fake()->numberBetween(7, 25) * 1000000,
            'started_at' => fake()->dateTimeBetween('-3 years', 'now'),
        ];
    }
}