<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MechanicJobController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return redirect()->route('dashboard'); });

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Auth
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pelanggan Booking
    Route::get('booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('my-orders', [BookingController::class, 'index'])->name('booking.index');
    Route::get('my-orders/{order}', [BookingController::class, 'show'])->name('booking.show');
    Route::patch('my-orders/{order}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    // Master Data (Admin & SA)
    Route::resource('customers', CustomerController::class)->except(['destroy']);
    Route::patch('customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])->name('customers.deactivate');

    Route::resource('vehicles', VehicleController::class)->except(['destroy']);
    Route::patch('vehicles/{vehicle}/deactivate', [VehicleController::class, 'deactivate'])->name('vehicles.deactivate');

    Route::resource('services', ServiceController::class)->except(['destroy']);
    Route::resource('service-categories', ServiceCategoryController::class)->except(['destroy']);
    
    Route::resource('spareparts', SparepartController::class)->except(['destroy']);
    Route::patch('spareparts/{sparepart}/status', [SparepartController::class, 'updateStatus'])->name('spareparts.status');

    // Service Orders (Admin & SA)
    Route::resource('service-orders', ServiceOrderController::class)->except(['destroy']);
    Route::patch('service-orders/{order}/assign', [ServiceOrderController::class, 'assignMechanic'])->name('service-orders.assign');
    Route::patch('service-orders/{order}/start', [ServiceOrderController::class, 'startService'])->name('service-orders.start');
    Route::patch('service-orders/{order}/cancel', [ServiceOrderController::class, 'cancel'])->name('service-orders.cancel');

    // Mechanic Jobs
    Route::get('mechanic-jobs', [MechanicJobController::class, 'index'])->name('mechanic-jobs.index');
    Route::get('mechanic-jobs/{order}', [MechanicJobController::class, 'show'])->name('mechanic-jobs.show');
    Route::patch('mechanic-jobs/{order}/start', [MechanicJobController::class, 'start'])->name('mechanic-jobs.start');
    Route::patch('mechanic-jobs/{order}', [MechanicJobController::class, 'update'])->name('mechanic-jobs.update');
    Route::patch('mechanic-jobs/{order}/finish', [MechanicJobController::class, 'finish'])->name('mechanic-jobs.finish');

    // Payments & Invoices
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{order}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{order}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('invoices/{payment}', [PaymentController::class, 'invoice'])->name('invoices.show');
});
