<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Huy Admin',
            'email' => 'admin@resort.com',
            'phone' => '0901234567',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'is_verified' => true,
        ]);

        $customerUser = User::create([
            'name' => 'Customer Test',
            'email' => 'customer@gmail.com',
            'phone' => '0988776655',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'is_verified' => true,
        ]);

        Customer::create([
            'user_id' => $customerUser->id,
            'loyalty_tier_id' => 1, // Bronze Tier default
            'full_name' => 'Nguyễn Khách Thử Nghiệm',
            'phone' => '0988776655',
            'email' => 'customer@gmail.com',
            'id_card' => '012345678912',
            'total_spent' => 0,
            'ai_preferences' => json_encode(['view' => 'ocean', 'diet' => 'none']),
        ]);
    }
}