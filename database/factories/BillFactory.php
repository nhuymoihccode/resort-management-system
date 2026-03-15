<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => fake()->unique()->numberBetween(1, 1000), 
            'transaction_id' => 'VNPAY-' . strtoupper(Str::random(12)), 
            'total_amount' => fake()->numberBetween(15, 100) * 100000,
            'payment_method' => fake()->randomElement(['cash', 'card', 'transfer', 'momo']),
            'payment_date' => fake()->dateTimeThisYear(),
        ];
    }
}