<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MomoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\OrderController;

// ── 1. PUBLIC ────────────────────────────────────────────────────
Route::get('/',           [HomeController::class, 'index'])->name('home');
Route::get('/rooms',      [HomeController::class, 'rooms'])->name('rooms.index');
Route::get('/rooms/{id}', [HomeController::class, 'showRoom'])->name('rooms.show');

// Search bar trang welcome
Route::post('/rooms/search', [HomeController::class, 'searchByDate'])->name('rooms.search');

// Checkout: tạo soft lock → hiện trang chọn dịch vụ
Route::post('/booking/checkout', [HomeController::class, 'checkout'])->name('booking.checkout');

// Sau khi login, Breeze redirect về đây → tiếp tục checkout
Route::get('/booking/resume', [HomeController::class, 'resumeAfterLogin'])
    ->middleware('auth')
    ->name('booking.resume');

// MoMo IPN webhook (ngoài CSRF, ngoài auth)
Route::post('/momo/ipn', [MomoController::class, 'ipn'])->name('momo.ipn');

// ── 2. CUSTOMER (bắt buộc đăng nhập) ────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');

    // Booking flow
    Route::post('/booking/store',              [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{order}/payment',     [BookingController::class, 'showPayment'])->name('booking.payment');
    Route::get('/booking/{order}/status',      [BookingController::class, 'pollStatus'])->name('booking.status');
    Route::post('/booking/{order}/cancel',     [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('/booking/{order}/cancel-holding', [BookingController::class, 'cancelHolding'])->name('booking.cancel-holding');
    Route::post('/booking/{order}/refresh-qr',     [BookingController::class, 'refreshQr'])->name('booking.refresh-qr');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── 3. ADMIN ─────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::resources([
        'zones'      => ZoneController::class,
        'rooms'      => RoomController::class,
        'services'   => ServiceController::class,
        'promotions' => PromotionController::class,
        'orders'     => OrderController::class,
    ]);

    Route::post('/booking/{order}/confirm-payment',
        [BookingController::class, 'confirmPayment']
    )->name('booking.confirm-payment');
});

require __DIR__.'/auth.php';