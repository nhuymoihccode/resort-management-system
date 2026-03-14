<?php

use Illuminate\Support\Facades\Route;

// Kéo các Controller vào đây 
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController; // <-- THÊM PROFILE CONTROLLER CỦA BREEZE
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\OrderController;

/*
|--------------------------------------------------------------------------
| 1. GIAO DIỆN KHÁCH HÀNG (PUBLIC) - Ai cũng vào được
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms.index');
Route::get('/rooms/{id}', [HomeController::class, 'showRoom'])->name('rooms.show');

/*
|--------------------------------------------------------------------------
| 2. LUỒNG ĐẶT PHÒNG & KHÁCH HÀNG (CUSTOMER) - Bắt buộc đăng nhập
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Trang quản lý cá nhân của khách
    Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');
    
    // Luồng booking (Tìm phòng -> Chọn -> Thanh toán)
    Route::post('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');

    // CÁC ROUTE PROFILE MẶC ĐỊNH CỦA BREEZE (Liên kết với các file bạn vừa đưa)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 3. GIAO DIỆN QUẢN TRỊ (ADMIN & STAFF) - Bắt buộc đăng nhập + Đúng Role
|--------------------------------------------------------------------------
*/
// Gom nhóm toàn bộ Route Admin: Tự động thêm tiền tố '/admin/' vào URL và chữ 'admin.' vào tên Route
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // Bảng điều khiển chung
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Quản lý Dữ liệu lõi (Master Data) bằng Resource 
    Route::resources([
        'zones'      => ZoneController::class,
        'rooms'      => RoomController::class,
        'services'   => ServiceController::class,
        'promotions' => PromotionController::class,
        'orders'     => OrderController::class,
    ]);
});

// File điều hướng Auth mặc định của Laravel Breeze (Đăng nhập, Đăng ký, Quên MK, Đăng xuất)
require __DIR__.'/auth.php';