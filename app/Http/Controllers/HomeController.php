<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $featuredRooms = Room::with('zone')->where('status', 'available')->inRandomOrder()->take(6)->get();
        $services = Service::all();
        
        $promotions = collect();
        $reviews = collect();

        $totalRooms = Room::count();
        $totalServices = Service::count();
        
        return view('welcome', compact('featuredRooms', 'services', 'promotions', 'reviews', 'totalRooms', 'totalServices'));
    }

    public function rooms()
    {
        $rooms = Room::with('zone')->where('status', 'available')->paginate(9);
        return view('rooms', compact('rooms'));
    }

    public function showRoom($id)
    {
        $room = Room::with('zone')->findOrFail($id);
        return view('room_detail', compact('room'));
    }
}