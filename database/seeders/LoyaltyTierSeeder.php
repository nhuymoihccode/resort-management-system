<?php

namespace Database\Seeders;

use App\Models\LoyaltyTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoyaltyTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            ['name' => 'Bronze', 'min_spend' => 0, 'discount_percent' => 0, 'perks' => json_encode(['Wifi tốc độ cao', 'Nước suối miễn phí'])],
            ['name' => 'Silver', 'min_spend' => 10000000, 'discount_percent' => 5, 'perks' => json_encode(['Buffet sáng', 'Trà chiều', 'Nhận phòng sớm'])],
            ['name' => 'Gold', 'min_spend' => 50000000, 'discount_percent' => 10, 'perks' => json_encode(['Nâng hạng phòng', 'Xe đưa đón sân bay', 'Spa miễn phí'])],
        ];

        foreach ($tiers as $tier) {
            LoyaltyTier::create($tier);
        }
    }
}
