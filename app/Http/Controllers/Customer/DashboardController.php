<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $customer = null; 
        $orders = collect(); // Danh sách đơn rỗng 

        return view('dashboard', compact('user', 'customer', 'orders'));
    }
}