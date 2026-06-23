<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-users');

        $users = User::with('roles')->latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('manage-users');

        $roles = Role::where('name', '!=', 'pelanggan')->orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => $data['status'],
        ]);

        $user->roles()->sync([$data['role_id']]);

        AuditLogService::log('Tambah user internal', 'users', $user->id);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        Gate::authorize('manage-users');

        $roles = Role::where('name', '!=', 'pelanggan')->orderBy('name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => $data['status'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);
        $user->roles()->sync([$data['role_id']]);

        AuditLogService::log('Ubah user internal', 'users', $user->id);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function deactivate(User $user)
    {
        Gate::authorize('manage-users');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Akun yang sedang login tidak dapat dinonaktifkan sendiri.');
        }

        $user->update(['status' => 'nonaktif']);

        AuditLogService::log('Nonaktifkan user internal', 'users', $user->id);

        return back()->with('success', 'User berhasil dinonaktifkan.');
    }
}
