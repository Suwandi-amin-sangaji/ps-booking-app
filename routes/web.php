<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('welcome');
});


// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Booking Routes
Route::get('/', [BookingController::class, 'index'])->name('booking.index');
Route::get('/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check-availability');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
// Route::get('/booking/payment/{booking}', [BookingController::class, 'payment'])->name('booking.payment');
// Route::get('/booking/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
// Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history');

// // Payment Routes
// Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
// Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
// Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');
// Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

// Payment Routes (Protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/booking/payment/{booking}', [BookingController::class, 'payment'])->name('booking.payment');
    Route::get('/booking/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history');

    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');
    Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    // Booking Management
    Route::get('/bookings', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/calendar', [App\Http\Controllers\Admin\BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::get('/bookings/calendar/events', [App\Http\Controllers\Admin\BookingController::class, 'getCalendarEvents'])->name('bookings.calendar.events');
    Route::get('/bookings/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/edit', [App\Http\Controllers\Admin\BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'update'])->name('bookings.update');
    Route::patch('/bookings/{booking}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::delete('/bookings/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('bookings.destroy');
});