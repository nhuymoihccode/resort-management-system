<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loyalty_tier_id',
        'total_spent',
        'phone',
        'address',
        'date_of_birth',
        'nationality',
        'notes',
    ];

    protected $casts = [
        'total_spent'    => 'integer',
        'date_of_birth'  => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loyaltyTier()
    {
        return $this->belongsTo(LoyaltyTier::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ── Business Logic ─────────────────────────────────────────────────────────

    /**
     * Tự động kiểm tra và nâng hạng tier sau khi total_spent thay đổi.
     * Gọi sau mỗi lần confirmPayment().
     */
    public function checkAndUpgradeTier(): void
    {
        $newTier = LoyaltyTier::where('min_spend', '<=', $this->total_spent)
            ->orderByDesc('min_spend')
            ->first();

        if ($newTier && $newTier->id !== $this->loyalty_tier_id) {
            $this->update(['loyalty_tier_id' => $newTier->id]);
        }
    }

    /**
     * Tính % tiến trình đến tier tiếp theo.
     */
    public function tierProgressPercent(): int
    {
        $current = $this->loyaltyTier;
        if (! $current) return 0;

        $next = LoyaltyTier::where('min_spend', '>', $current->min_spend)
            ->orderBy('min_spend')
            ->first();

        if (! $next) return 100; // đã đạt tier cao nhất

        $spent    = $this->total_spent - $current->min_spend;
        $required = $next->min_spend   - $current->min_spend;

        return (int) min(100, round($spent / $required * 100));
    }

    /**
     * Số tiền còn cần chi để lên tier tiếp theo.
     */
    public function amountToNextTier(): ?int
    {
        $current = $this->loyaltyTier;
        if (! $current) return null;

        $next = LoyaltyTier::where('min_spend', '>', $current->min_spend)
            ->orderBy('min_spend')
            ->first();

        if (! $next) return null;

        return max(0, $next->min_spend - $this->total_spent);
    }
}