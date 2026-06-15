<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MotoCare
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Register Pelanggan
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    Route::resource('customers', CustomerController::class)->except(['destroy']);
    Route::patch('customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])->name('customers.deactivate');

    // Vehicles
    Route::resource('vehicles', VehicleController::class)->except(['destroy']);
    Route::patch('vehicles/{vehicle}/deactivate', [VehicleController::class, 'deactivate'])->name('vehicles.deactivate');
    // Jika Anda membuat controller method untuk upload terpisah (atau disatukan di store/update seperti contoh di atas)
    // Route::post('vehicles/{vehicle}/photo', [VehicleController::class, 'uploadPhoto'])->name('vehicles.photo');

    // Services
    Route::resource('services', ServiceController::class)->except(['destroy']);
    
    // Service Categories
    Route::resource('service-categories', ServiceCategoryController::class)->except(['destroy']);

    // Spareparts
    Route::resource('spareparts', SparepartController::class)->except(['destroy']);
    Route::patch('spareparts/{sparepart}/status', [SparepartController::class, 'updateStatus'])->name('spareparts.status');
});