<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->word() . '_setting',
            'value' => fake()->word(),
            'group' => fake()->randomElement(['general', 'payment', 'booking']),
        ];
    }
}