<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'total_amount',
        'payment_method',
        'payment_date',
        'bank_payload',
        'qr_image_url',
        'confirm_status',
        'confirmed_at',
        'confirmed_by',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'bank_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}