<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['percent', 'fixed']);
        $discount = $type === 'percent' ? fake()->randomElement([10, 15, 20]) : fake()->randomElement([200000, 500000]);

        return [
            'code' => strtoupper(fake()->unique()->bothify('SUMMER-####')),
            'discount_value' => $discount,
            'type' => $type,
            'start_date' => now()->subDays(rand(0, 10)),
            'end_date' => now()->addDays(rand(10, 30)),
        ];
    }
}