<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy customer record kèm loyalty tier
        $customer = Customer::with('loyaltyTier')
            ->where('user_id', $user->id)
            ->first();

        // Lấy orders — bỏ holding, kèm đầy đủ thông tin
        $orders = Order::where('user_id', $user->id)
            ->whereNotIn('status', ['holding'])
            ->with(['room.zone', 'bill'])
            ->withCount('services')
            ->latest()
            ->get();

        // Tính stats
        $totalSpent    = $orders->where('payment_status', 'paid')->sum('total_price');
        $confirmedCount= $orders->where('status', 'confirmed')->count();
        $pendingCount  = $orders->whereIn('status', ['pending'])->count();

        // Loyalty tier info
        $tierInfo = [
            1 => ['name' => 'Thành viên', 'color' => 'slate',  'icon' => '🌿', 'next_at' => 10000000],
            2 => ['name' => 'Bạc',        'color' => 'blue',   'icon' => '⭐', 'next_at' => 50000000],
            3 => ['name' => 'Vàng',       'color' => 'amber',  'icon' => '👑', 'next_at' => null],
        ];
        $tierId      = $customer?->loyalty_tier_id ?? 1;
        $currentTier = $tierInfo[$tierId] ?? $tierInfo[1];
        $nextTier    = $tierInfo[$tierId + 1] ?? null;
        $progress    = 0;
        if ($nextTier && $customer) {
            $prevMin  = $tierInfo[$tierId]['next_at'] ?? 0;
            $progress = $prevMin > 0
                ? min(100, (int)(($customer->total_spent / $nextTier['next_at']) * 100))
                : min(100, (int)(($customer->total_spent / 10000000) * 100));
        }

        return view('dashboard', compact(
            'user', 'customer', 'orders',
            'totalSpent', 'confirmedCount', 'pendingCount',
            'currentTier', 'nextTier', 'tierId', 'progress'
        ));
    }
}