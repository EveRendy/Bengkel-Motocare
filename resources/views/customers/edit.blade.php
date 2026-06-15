@extends('layouts.app')
@section('title', 'Edit Pelanggan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('customers.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Edit Pelanggan: {{ $customer->customer_code }}</h1>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
        <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                    @error('email') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">No HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>{{ old('address', $customer->address) }}</textarea>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Status</label>
                <select name="status" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                    <option value="aktif" {{ $customer->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $customer->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="pt-4">
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow">Update Data</button>
            </div>
        </form>
    </div>
</div>
@endsection