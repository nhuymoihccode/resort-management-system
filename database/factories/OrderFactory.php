<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Random check-in từ 3 tháng trước đến 3 tháng sau
        $checkIn = fake()->dateTimeBetween('-3 months', '+3 months');
        $checkOut = Carbon::parse($checkIn)->addDays(fake()->numberBetween(1, 5));

        return [
            'customer_id' => fake()->numberBetween(1, 30), // Giả sử có 30 khách
            'room_id' => fake()->numberBetween(1, 25),     // Giả sử có 25 phòng
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_guests' => fake()->numberBetween(1, 4),
            'total_price' => fake()->numberBetween(15, 100) * 100000, // Từ 1.5tr đến 10tr
            'payment_status' => fake()->randomElement(['unpaid', 'paid', 'refunded']),
            'status' => fake()->randomElement(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled']),
            'is_ai_compensated' => fake()->boolean(5), // 5% đơn hàng phải bồi thường (thực tế)
        ];
    }
}
