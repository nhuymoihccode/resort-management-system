<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['standard', 'suite', 'villa', 'bungalow']);
        
        // Map to 4 pre-defined Zones in the database
        $zoneId = fake()->numberBetween(1, 4); 

        // Realistic view mapping based on assigned Zone
        $view = match($zoneId) {
            1 => fake()->randomElement(['Trực diện biển', 'Góc nhìn biển', 'Hướng hồ bơi vô cực']), // Ocean Zone
            2 => fake()->randomElement(['Hướng vườn cây nhiệt đới', 'Hướng lối đi nội bộ']),      // Garden Zone
            3 => fake()->randomElement(['Trực diện hồ bơi', 'Góc hồ bơi riêng']),                 // Pool Zone
            4 => fake()->randomElement(['Hướng sảnh chính', 'Hướng đồi']),                        // Central Zone
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
            'view' => $view,
            'area' => fake()->numberBetween(40, 150), 
            'status' => fake()->randomElement(['available', 'occupied', 'cleaning', 'maintenance']),
        ];
    }
}