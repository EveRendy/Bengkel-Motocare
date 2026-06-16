@extends('layouts.app')
@section('title', 'Buat Order Servis Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('service-orders.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Buat Order Servis Baru</h1>
    </div>

    <form action="{{ route('service-orders.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Informasi Kendaraan & Pelanggan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Pelanggan</label>
                    <select name="customer_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }} ({{ $cust->customer_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Kendaraan</label>
                    <select name="vehicle_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($vehicles as $veh)
                            <option value="{{ $veh->id }}" {{ old('vehicle_id') == $veh->id ? 'selected' : '' }}>{{ $veh->license_plate }} - {{ $veh->customer->name ?? '' }}</option>
                        @endforeach
                    </select>
                    @error('vehicle_id') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tanggal Servis</label>
                    <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Kilometer Saat Ini</label>
                    <input type="number" name="current_km" value="{{ old('current_km', 0) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-400 mb-1">Keluhan Pelanggan</label>
                    <textarea name="complaint" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>{{ old('complaint') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Pilih Layanan Servis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-2">
                @foreach($services as $srv)
                <label class="flex items-center gap-3 p-3 bg-gray-900 border border-gray-700 rounded-lg cursor-pointer hover:border-orange-500/50">
                    <input type="checkbox" name="services[]" value="{{ $srv->id }}" class="w-5 h-5 text-orange-500 rounded bg-gray-800 border-gray-600 focus:ring-orange-500/50" {{ (is_array(old('services')) && in_array($srv->id, old('services'))) ? 'checked' : '' }}>
                    <div class="flex-1 flex justify-between">
                        <span class="text-gray-200 text-sm">{{ $srv->name }}</span>
                        <span class="text-orange-400 text-sm font-bold">Rp {{ number_format($srv->price, 0, ',', '.') }}</span>
                    </div>
                </label>
                @endforeach
            </div>
            @error('services') <span class="text-red-400 text-xs mt-2 block">{{ $message }}</span> @enderror
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Penugasan & Catatan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tugaskan Mekanik (Opsional)</label>
                    <select name="mechanic_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                        <option value="">-- Nanti Saja --</option>
                        @foreach($mechanics as $mekanik)
                            <option value="{{ $mekanik->id }}" {{ old('mechanic_id') == $mekanik->id ? 'selected' : '' }}>{{ $mekanik->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Catatan Internal (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg">Buat Order Servis</button>
    </form>
</div>
@endsection