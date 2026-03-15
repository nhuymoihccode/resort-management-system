<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $serviceName = fake()->unique()->randomElement([
            'Ăn sáng tại phòng', 
            'Dịch vụ Spa', 
            'Thuê xe điện', 
            'Giặt ủi cao cấp', 
            'Tour tham quan vịnh'
        ]);

        $details = match($serviceName) {
            'Ăn sáng tại phòng'   => ['price' => 250000,  'unit' => 'person'],
            'Dịch vụ Spa'         => ['price' => 800000,  'unit' => 'turn'], 
            'Thuê xe điện'        => ['price' => 100000,  'unit' => 'hour'], 
            'Giặt ủi cao cấp'     => ['price' => 150000,  'unit' => 'turn'],
            'Tour tham quan vịnh' => ['price' => 1200000, 'unit' => 'person'],
        };

        return [
            'name' => $serviceName,
            'price' => $details['price'],
            'unit' => $details['unit'],
        ];
    }
}