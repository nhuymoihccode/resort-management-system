<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ResortInfo;
use App\Models\Zone;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Order;
use App\Models\Bill;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LoyaltyTierSeeder::class,
            UserSeeder::class,
        ]);

        $faker = Faker::create('vi_VN');

        
        $resort = ResortInfo::create([
            'name' => 'Resort Pro',
            'address' => 'Nha Trang, Vietnam',
            'phone' => '0123456789',
            'email' => 'contact@resortpro.com',
        ]);


        Zone::insert([
            ['resort_info_id' => $resort->id, 'name' => 'Khu Biển (Ocean View)', 'description' => 'Các Villa và Bungalow sát bờ biển riêng', 'created_at' => now(), 'updated_at' => now()],
            ['resort_info_id' => $resort->id, 'name' => 'Khu Vườn (Garden View)', 'description' => 'Bao quanh bởi rừng nhiệt đới', 'created_at' => now(), 'updated_at' => now()],
            ['resort_info_id' => $resort->id, 'name' => 'Khu Bể Bơi (Pool Side)', 'description' => 'Gần bể bơi vô cực trung tâm', 'created_at' => now(), 'updated_at' => now()],
            ['resort_info_id' => $resort->id, 'name' => 'Khu Trung Tâm (Center)', 'description' => 'Gần sảnh Lễ tân, Nhà hàng Buffet', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Service::insert([
            ['name' => 'Ăn sáng Buffet Hải Sản', 'price' => 350000, 'unit' => 'person', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Spa & Massage Body 60 phút', 'price' => 800000, 'unit' => 'turn', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Massage Chân & Ấn huyệt 45 phút', 'price' => 450000, 'unit' => 'turn', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tour Lặn Biển Ngắm San Hô', 'price' => 1200000, 'unit' => 'person', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chèo thuyền Kayak trên biển', 'price' => 200000, 'unit' => 'hour', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lướt ván phản lực (Jet Ski)', 'price' => 800000, 'unit' => 'hour', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Thuê Xe Đạp Đôi dạo biển', 'price' => 50000, 'unit' => 'hour', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dịch vụ Giặt ủi cao cấp (Gói)', 'price' => 150000, 'unit' => 'turn', 'created_at' => now(), 'updated_at' => now()],
        ]);


        $customers = [];
        for ($i = 0; $i < 30; $i++) {
            $spent = $faker->numberBetween(0, 100000000);
            $tierId = 1; 
            if ($spent >= 50000000) $tierId = 3; 
            elseif ($spent >= 10000000) $tierId = 2; 

            $customers[] = Customer::create([
                'loyalty_tier_id' => $tierId,
                'full_name' => $faker->name(),
                'phone' => $faker->phoneNumber(),
                'email' => $faker->unique()->safeEmail(),
                'id_card' => $faker->numerify('0010########'),
                'total_spent' => $spent,
                'ai_preferences' => json_encode(['view' => 'ocean']),
            ]);
        }


        $roomTypes = ['standard', 'suite', 'villa', 'bungalow']; 
        $rooms = [];
        for ($i = 0; $i < 25; $i++) {
            $type = $faker->randomElement($roomTypes);
            $price = 1500000;
            $viewDesc = 'Hướng Vườn - 1 Giường đôi';
            
            if ($type === 'villa') { $price = 6500000; $viewDesc = 'Hướng Biển - 2 Phòng ngủ'; }
            elseif ($type === 'suite') { $price = 3500000; $viewDesc = 'Hướng Hồ Bơi - Có phòng khách'; }
            elseif ($type === 'bungalow') { $price = 2800000; $viewDesc = 'Hướng Biển - Sân vườn riêng'; }

            $rooms[] = Room::create([
                'zone_id' => rand(1, 4),
                'room_number' => $faker->unique()->numberBetween(101, 999),
                'type' => $type,
                'price' => $price,
                'capacity_adults' => rand(2, 4),
                'capacity_children' => rand(0, 2),
                'view' => $viewDesc, 
                'status' => 'available',
            ]);
        }


        for ($i = 0; $i < 500; $i++) {
            $room = $faker->randomElement($rooms);
            $customer = $faker->randomElement($customers);
            
            $randomDate = Carbon::now()->subDays(rand(1, 180));
            $checkIn = $randomDate->copy();
            $checkOut = $randomDate->copy()->addDays(rand(1, 4));

            $daysStayed = $checkIn->diffInDays($checkOut) ?: 1;
            $totalPrice = $room->price * $daysStayed;

            $order = Order::create([
                'customer_id' => $customer->id,
                'room_id' => $room->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'total_guests' => rand(1, 4),
                'total_price' => $totalPrice,
                'status' => 'checked_out', 
                'payment_status' => 'paid',
                'created_at' => $checkIn,
                'updated_at' => $checkOut,
            ]);

            Bill::create([
                'order_id' => $order->id,
                'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                'total_amount' => $totalPrice,
                'payment_method' => $faker->randomElement(['cash', 'card', 'transfer']), 
                'payment_date' => $checkOut,
                'created_at' => $checkOut,
                'updated_at' => $checkOut,
            ]);
        }
        \App\Models\Staff::factory(20)->create();     
        \App\Models\Promotion::factory(10)->create(); 
        \App\Models\Review::factory(200)->create();   
        \App\Models\Setting::factory(5)->create();
    }
}