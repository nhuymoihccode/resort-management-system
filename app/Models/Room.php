<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id', 'room_number', 'slug',
        'type', 'price', 'status',
        'capacity_adults', 'capacity_children',
        'view', 'area',
    ];

    // ── Slug auto-generate ────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Room $room) {
            $room->slug = $room->slug ?? static::generateUniqueSlug($room->room_number);
        });

        static::updating(function (Room $room) {
            if ($room->isDirty('room_number') || empty($room->slug)) {
                $room->slug = static::generateUniqueSlug($room->room_number, $room->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $roomNumber, ?int $excludeId = null): string
    {
        $base    = 'phong-' . Str::slug($roomNumber);
        $slug    = $base;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->clone()->exists()) {
            $slug  = $base . '-' . $counter++;
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Route Model Binding dùng slug thay id.
     * /rooms/{room} → Laravel tự tìm theo slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}