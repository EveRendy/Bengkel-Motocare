@extends('layouts.app')
@section('title', 'Tambah User')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-100">Tambah User Internal</h1>
        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-200 rounded-lg text-sm">Kembali</a>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm text-gray-400 mb-2">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                <x-ui.field-error name="name" />
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                <x-ui.field-error name="email" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Password</label>
                    <input type="password" name="password" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                    <x-ui.field-error name="password" />
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Role</label>
                    <select name="role_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                        <option value="">Pilih role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ str_replace('_', ' ', strtoupper($role->name)) }}</option>
                        @endforeach
                    </select>
                    <x-ui.field-error name="role_id" />
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Status</label>
                    <select name="status" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <x-ui.field-error name="status" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-700">
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-gray-400 hover:text-gray-200">Batal</a>
                <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium">Simpan User</button>
            </div>
        </form>
    </div>
</div>
@endsection
