<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => fake()->unique()->numberBetween(1, 1000), 
            'transaction_id' => 'VNPAY-' . strtoupper(Str::random(12)), // Giả lập cổng thanh toán VNPay
            'total_amount' => fake()->numberBetween(15, 100) * 100000,
            'payment_method' => fake()->randomElement(['cash', 'card', 'transfer', 'momo']),
            'payment_date' => fake()->dateTimeThisYear(),
        ];
    }
}
