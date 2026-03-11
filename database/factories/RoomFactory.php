<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['standard', 'suite', 'villa', 'bungalow']);
        
        // Random từ 1 đến 4, tương ứng với 4 Zone bạn đã tạo trong DB
        $zoneId = fake()->numberBetween(1, 4); 

        // Gán View chi tiết dựa trên Zone để logic khớp với thực tế
        $view = match($zoneId) {
            1 => fake()->randomElement(['Trực diện biển', 'Góc nhìn biển', 'Hướng hồ bơi vô cực']), // Khu Biển
            2 => fake()->randomElement(['Hướng vườn cây nhiệt đới', 'Hướng lối đi nội bộ']),      // Khu Vườn
            3 => fake()->randomElement(['Trực diện hồ bơi', 'Góc hồ bơi riêng']),                 // Khu Bể Bơi
            4 => fake()->randomElement(['Hướng sảnh chính', 'Hướng đồi']),                        // Khu Trung Tâm
        };

        $price = match($type) {
            'standard' => 1200000,
            'bungalow' => 3800000,
            'suite'    => 2500000,
            'villa'    => 6500000,
        };

        return [
            'zone_id' => $zoneId, 
            'room_number' => fake()->unique()->numberBetween(101, 999),
            'type' => $type,
            'price' => $price,
            'capacity_adults' => fake()->numberBetween(2, 4), 
            'capacity_children' => fake()->numberBetween(0, 2), 
            'view' => $view, // View giờ đã logic với Zone
            'area' => fake()->numberBetween(40, 150), 
            'status' => fake()->randomElement(['available', 'occupied', 'cleaning', 'maintenance']),
        ];
    }
}
