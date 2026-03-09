<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResortInfo extends Model
{
    use HasFactory;

    protected $table = 'resort_info';
    protected $fillable = ['name','address', 'email', 'phone', 'description'];
    public function zones(){return $this->hasMany(Zone::class); }
    public function staffs(){return $this->hasMany(Staff::class); }
}
