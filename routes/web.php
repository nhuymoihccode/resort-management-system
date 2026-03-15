<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\OrderController;

/*1. GIAO DIỆN KHÁCH HÀNG (PUBLIC)*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms.index');
Route::get('/rooms/{id}', [HomeController::class, 'showRoom'])->name('rooms.show');

/*2. LUỒNG ĐẶT PHÒNG & KHÁCH HÀNG (CUSTOMER) - Bắt buộc đăng nhập*/
Route::middleware(['auth'])->group(function () {
    // Trang quản lý cá nhân của khách
    Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');
    
    // Luồng booking (Tìm phòng -> Chọn -> Thanh toán)
    Route::post('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*3. GIAO DIỆN QUẢN TRỊ (ADMIN & STAFF) - Bắt buộc đăng nhập + Đúng Role*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::resources([
        'zones'      => ZoneController::class,
        'rooms'      => RoomController::class,
        'services'   => ServiceController::class,
        'promotions' => PromotionController::class,
        'orders'     => OrderController::class,
    ]);
});

require __DIR__.'/auth.php';