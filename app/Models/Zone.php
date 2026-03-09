<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = ['resort_info_id', 'name', 'description'];

    public function resort() { 
        return $this->belongsTo(ResortInfo::class, 'resort_info_id'); 
    }
    public function rooms() { 
        return $this->hasMany(Room::class); 
    }
}
