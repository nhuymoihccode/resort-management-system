<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        // Random check-in date within a 6-month window (+/- 3 months)
        $checkIn = fake()->dateTimeBetween('-3 months', '+3 months');
        $checkOut = Carbon::parse($checkIn)->addDays(fake()->numberBetween(1, 5));

        return [
            'customer_id' => fake()->numberBetween(1, 30), // Assuming 30 seeded customers
            'room_id' => fake()->numberBetween(1, 25),     // Assuming 25 seeded rooms
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_guests' => fake()->numberBetween(1, 4),
            'total_price' => fake()->numberBetween(15, 100) * 100000, // Price range: 1.5M to 10M VND
            'payment_status' => fake()->randomElement(['unpaid', 'paid', 'refunded']),
            'status' => fake()->randomElement(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled']),
            'is_ai_compensated' => fake()->boolean(5), // 5% probability of AI compensation
        ];
    }
}