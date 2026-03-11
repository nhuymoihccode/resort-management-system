<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $spent = fake()->numberBetween(0, 100000000);

        $tierId = 1;
        if ($spent >= 50000000) {
            $tierId = 3;
        } elseif ($spent >= 10000000) {
            $tierId = 2;
        }

        return [
            'user_id' => null,
            'loyalty_tier_id' => $tierId,
            'full_name' => fake('vi_VN')->name(),
            'phone' => fake('vi_VN')->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'id_card' => fake()->numerify('0010########'),
            'total_spent' => $spent,
            'ai_preferences' => json_encode([
                'view_preference' => fake()->randomElement(['ocean', 'garden', 'mountain', 'any']),
                'dietary_tags' => fake()->randomElements(['vegetarian', 'halal', 'seafood_allergy', 'peanut_allergy', 'none'], rand(1, 2)),
                'customer_note' => fake()->randomElement([
                    'Tôi bị dị ứng hải sản nặng, vui lòng báo bếp.',
                    'Gia đình tôi có người theo đạo Hồi (Halal), xin lưu ý.',
                    'Cho mình xin phòng tầng cao, yên tĩnh nhé.',
                    'Không ăn được hành tỏi.',
                    '', // Trường hợp khách không ghi chú gì
                ])
            ]),
        ];
    }
}
