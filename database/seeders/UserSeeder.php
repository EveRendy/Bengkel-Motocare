<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@motocare.com',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role' => 'admin',
            ],
            [
                'name' => 'Service Advisor',
                'email' => 'advisor@motocare.com',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role' => 'service_advisor',
            ],
            [
                'name' => 'Mekanik',
                'email' => 'mekanik@motocare.com',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role' => 'mekanik',
            ],
            [
                'name' => 'Kasir',
                'email' => 'kasir@motocare.com',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role' => 'kasir',
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@motocare.com',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role' => 'manager',
            ],
            [
                'name' => 'Pelanggan User',
                'email' => 'pelanggan@motocare.com',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role' => 'pelanggan',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $role = Role::where('name', $roleName)->first();
            if ($role && !$user->roles()->where('role_id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }

            // Buat record customer untuk pelanggan
            if ($roleName === 'pelanggan') {
                Customer::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'customer_code' => 'CUST-0001',
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => '081234567890',
                        'address' => 'Jl. Contoh No. 1, Jakarta',
                        'registration_date' => now()->toDateString(),
                        'status' => 'aktif',
                    ]
                );
            }
        }
    }
}