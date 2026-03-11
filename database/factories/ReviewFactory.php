<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = fake()->numberBetween(3, 5); // Khách thường rate 3-5 sao
        $sentiment = $rating >= 4 ? 'positive' : 'neutral';

        return [
            'customer_id' => fake()->numberBetween(1, 30),
            'order_id' => fake()->unique()->numberBetween(1, 500),
            'rating' => $rating,
            'comment' => fake('vi_VN')->realText(100), // Đoạn text tiếng Việt có ý nghĩa
            'ai_sentiment' => $sentiment,
        ];
    }
}
