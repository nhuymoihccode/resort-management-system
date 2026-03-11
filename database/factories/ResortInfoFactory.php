<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResortInfo>
 */
class ResortInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Resort Test',
            'address' => fake('vi_VN')->address(),
            'phone' => fake('vi_VN')->phoneNumber(),
            'email' => fake()->safeEmail(),
        ];
    }
}
