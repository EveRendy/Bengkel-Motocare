<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Global
        Gate::define('view-dashboard', fn($user) => true);

        // Customers
        Gate::define('manage-customers', fn($user) => $user->hasRole('admin') || $user->hasRole('service_advisor'));
        Gate::define('view-customer', function ($user, $customer) {
            if ($user->hasRole('admin') || $user->hasRole('service_advisor')) return true;
            return $user->hasRole('pelanggan') && $customer->user_id === $user->id;
        });

        // Vehicles
        Gate::define('manage-vehicles', fn($user) => $user->hasRole('admin') || $user->hasRole('service_advisor'));
        Gate::define('view-vehicle', function ($user, $vehicle) {
            if ($user->hasRole('admin') || $user->hasRole('service_advisor')) return true;
            if ($user->hasRole('pelanggan')) return $vehicle->customer && $vehicle->customer->user_id === $user->id;
            // Mekanik bisa lihat jika kendaraan terkait order miliknya
            if ($user->hasRole('mekanik')) {
                return $vehicle->serviceOrders()->where('mechanic_id', $user->id)->exists();
            }
            return false;
        });

        // Services & Spareparts
        Gate::define('manage-services', fn($user) => $user->hasRole('admin'));
        Gate::define('manage-spareparts', fn($user) => $user->hasRole('admin'));

        // Mechanics & Cashiers
        Gate::define('handle-mechanic-job', fn($user, $job) => $user->hasRole('mekanik') && $job->mechanic_id === $user->id);
        Gate::define('process-payment', fn($user) => $user->hasRole('kasir'));
        Gate::define('cancel-payment', fn($user) => $user->hasRole('admin') || $user->hasRole('manager'));
        Gate::define('view-reports', fn($user) => $user->hasRole('manager') || $user->hasRole('admin'));
        Gate::define('view-audit-logs', fn($user) => $user->hasRole('manager') || $user->hasRole('admin'));
        Gate::define('manage-settings', fn($user) => $user->hasRole('manager') || $user->hasRole('admin'));

        Gate::define('create-booking', fn($user) => $user->hasRole('pelanggan'));
        
        Gate::define('manage-orders', fn($user) => $user->hasRole('admin') || $user->hasRole('service_advisor'));
        
        Gate::define('view-order', function ($user, $order) {
            if ($user->hasRole('admin') || $user->hasRole('service_advisor') || $user->hasRole('kasir')) return true;
            if ($user->hasRole('mekanik')) return $order->mechanic_id === $user->id;
            return $user->hasRole('pelanggan') && $order->customer->user_id === $user->id;
        });
        
        Gate::define('cancel-order', function ($user, $order) {
            if (!in_array($order->status, ['terdaftar', 'menunggu'])) return false;
            if ($user->hasRole('admin') || $user->hasRole('service_advisor')) return true;
            return $user->hasRole('pelanggan') && $order->customer->user_id === $user->id;
        });
        
        Gate::define('assign-mechanic', fn($user) => $user->hasRole('admin') || $user->hasRole('service_advisor'));
        
        // Manajemen Kendaraan Milik Sendiri (Pelanggan)
        Gate::define('manage-own-vehicles', function ($user) {
            return $user->hasRole('pelanggan');
        });
        Gate::define('view-own-vehicle', function ($user, $vehicle) {
            return $user->hasRole('pelanggan') && $vehicle->customer && $vehicle->customer->user_id === $user->id;
        });
        Gate::define('update-own-vehicle', function ($user, $vehicle) {
            return $user->hasRole('pelanggan') && $vehicle->customer && $vehicle->customer->user_id === $user->id;
        });

        // Mechanic Jobs (Modul Mekanik)
        Gate::define('view-mechanic-jobs', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('service_advisor') || $user->hasRole('mekanik');
        });

        Gate::define('view-mechanic-job', function ($user, $job) {
            if ($user->hasRole('admin') || $user->hasRole('service_advisor')) return true;
            return $user->hasRole('mekanik') && $job->mechanic_id === $user->id;
        });

        Gate::define('update-mechanic-job', function ($user, $job) {
            return $user->hasRole('mekanik') && $job->mechanic_id === $user->id && $job->serviceOrder->status === 'dikerjakan';
        });

        Gate::define('complete-mechanic-job', function ($user, $job) {
            return $user->hasRole('mekanik') && $job->mechanic_id === $user->id && $job->serviceOrder->status === 'dikerjakan';
        });

        // Booking Flow
        Gate::define('process-booking', fn($user) => $user->hasRole('admin') || $user->hasRole('service_advisor'));
        Gate::define('view-bookings', fn($user) => $user->hasRole('admin') || $user->hasRole('service_advisor'));
    
    }
}
