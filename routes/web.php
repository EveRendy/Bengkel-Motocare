<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
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
    Route::resource('service-orders', ServiceOrderController::class)->parameters(['service-orders' => 'order'])->except(['destroy']);
    Route::patch('service-orders/{order}/assign', [ServiceOrderController::class, 'assignMechanic'])->name('service-orders.assign');
    Route::patch('service-orders/{order}/start', [ServiceOrderController::class, 'startService'])->name('service-orders.start');
    Route::patch('service-orders/{order}/cancel', [ServiceOrderController::class, 'cancel'])->name('service-orders.cancel');

    // KENDARAAN SAYA (PELANGGAN)
    Route::get('my-vehicles', [VehicleController::class, 'myVehicles'])->name('my-vehicles.index');
    Route::get('my-vehicles/create', [VehicleController::class, 'myCreate'])->name('my-vehicles.create');
    Route::post('my-vehicles', [VehicleController::class, 'myStore'])->name('my-vehicles.store');
    Route::get('my-vehicles/{vehicle}', [VehicleController::class, 'myShow'])->name('my-vehicles.show');
    Route::get('my-vehicles/{vehicle}/edit', [VehicleController::class, 'myEdit'])->name('my-vehicles.edit');
    Route::put('my-vehicles/{vehicle}', [VehicleController::class, 'myUpdate'])->name('my-vehicles.update');

    // MEKANIK + ADMIN + SA (Lihat daftar & detail pekerjaan)
    Route::middleware(['role:mekanik,admin,service_advisor'])->group(function () {
        Route::get('mechanic-jobs', [\App\Http\Controllers\MechanicJobController::class, 'index'])->name('mechanic-jobs.index');
        Route::get('mechanic-jobs/{job}', [\App\Http\Controllers\MechanicJobController::class, 'show'])->name('mechanic-jobs.show');
    });

    // MEKANIK ONLY (Input hasil & input sparepart)
    Route::middleware(['role:mekanik'])->group(function () {
        Route::get('mechanic-jobs/{job}/edit', [\App\Http\Controllers\MechanicJobController::class, 'edit'])->name('mechanic-jobs.edit');
        Route::put('mechanic-jobs/{job}', [\App\Http\Controllers\MechanicJobController::class, 'update'])->name('mechanic-jobs.update');
        Route::patch('mechanic-jobs/{job}/complete', [\App\Http\Controllers\MechanicJobController::class, 'complete'])->name('mechanic-jobs.complete');
        
        Route::post('mechanic-jobs/{job}/spareparts', [\App\Http\Controllers\MechanicJobController::class, 'storeSparepart'])->name('mechanic-jobs.spareparts.store');
        Route::delete('mechanic-jobs/{job}/spareparts/{sparepart}', [\App\Http\Controllers\MechanicJobController::class, 'removeSparepart'])->name('mechanic-jobs.spareparts.remove');
    });

    // =======================================================
    // BOOKING DARI PELANGGAN (Dilihat oleh Admin / SA)
    // =======================================================
    Route::middleware(['role:admin,service_advisor'])->group(function () {
        Route::get('bookings', [\App\Http\Controllers\ServiceOrderController::class, 'bookings'])->name('bookings.index');
        Route::get('bookings/{order}/process', [\App\Http\Controllers\ServiceOrderController::class, 'processBookingForm'])->name('bookings.process.form');
        Route::put('bookings/{order}/process', [\App\Http\Controllers\ServiceOrderController::class, 'processBooking'])->name('bookings.process');
    });

        // MEKANIK ONLY (Input hasil & input sparepart)
    Route::middleware(['role:mekanik'])->group(function () {
        // ... (route lain biarkan)

        // BARU: Mekanik klik mulai pengerjaan
    Route::patch('mechanic-jobs/{job}/start', [\App\Http\Controllers\MechanicJobController::class, 'startJob'])->name('mechanic-jobs.start');
        
        // ...
    });
});