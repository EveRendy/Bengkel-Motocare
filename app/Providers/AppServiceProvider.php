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
        Gate::define('view-reports', fn($user) => $user->hasRole('manager') || $user->hasRole('admin'));
    }
}