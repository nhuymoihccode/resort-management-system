<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'resort_info_id' => 1,
            'name' => 'Khu ' . fake()->unique()->word(),
            'description' => fake()->sentence(),
        ];
    }
}