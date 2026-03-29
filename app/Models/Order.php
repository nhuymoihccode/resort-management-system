<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'room_id',
        'user_id',
        'check_in',
        'check_out',
        'total_guests',
        'total_price',
        'deposit_amount',
        'payment_status',
        'status',
        'canceled_at',
        'is_ai_compensated',
        'expires_at',
        'transfer_code',
        'note',
    ];

    protected $casts = [
        'check_in'    => 'datetime',
        'check_out'   => 'datetime',
        'expires_at'  => 'datetime',
        'canceled_at' => 'datetime',
    ];

    // ── UUID auto-generate ────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Route Model Binding dùng uuid thay id.
     * /booking/{order} → Laravel tự tìm theo uuid, không phải id số.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'order_service')
                    ->withPivot('quantity', 'price_at_time')
                    ->withTimestamps();
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}