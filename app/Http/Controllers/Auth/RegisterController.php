<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();

        try {
            // Buat User
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 'aktif',
            ]);

            // Assign Role Pelanggan
            $role = Role::where('name', 'pelanggan')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            // Generate Customer Code unik (CUST-Timestamp)
            $customerCode = 'CUST-' . time();

            // Buat Customer
            Customer::create([
                'user_id' => $user->id,
                'customer_code' => $customerCode,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'registration_date' => now()->toDateString(),
                'status' => 'aktif',
            ]);

            // Login otomatis
            Auth::login($user);
            AuditLogService::log('Registrasi akun pelanggan', 'auth', $user->id);

            return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang di MotoCare.');
        } catch (\Exception $e) {
            report($e);
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.']);
        }
    }
}
