<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'min_spend', 'discount_percent', 'perks'];
    protected $casts = ['perks' => 'array']; 

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
