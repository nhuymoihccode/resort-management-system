<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        // Typical rating behavior: leaning towards 3-5 stars
        $rating = fake()->numberBetween(3, 5); 
        $sentiment = $rating >= 4 ? 'positive' : 'neutral';

        return [
            'customer_id' => fake()->numberBetween(1, 30),
            'order_id' => fake()->unique()->numberBetween(1, 500),
            'rating' => $rating,
            'comment' => fake('vi_VN')->realText(100), // Meaningful Vietnamese review text
            'ai_sentiment' => $sentiment,
        ];
    }
}