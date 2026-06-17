@extends('layouts.app')
@section('title', 'Edit Order Servis')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('service-orders.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Edit Order: {{ $order->order_number }}</h1>
    </div>

    <form action="{{ route('service-orders.update', $order) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Informasi Kendaraan & Pelanggan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Pelanggan</label>
                    <input type="text" value="{{ $order->customer->name }} ({{ $order->customer->customer_code }})" class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-gray-500 cursor-not-allowed" disabled>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Kendaraan</label>
                    <input type="text" value="{{ $order->vehicle->license_plate }} - {{ $order->vehicle->brand }}" class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-gray-500 cursor-not-allowed" disabled>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tanggal Servis</label>
                    <input type="date" name="order_date" value="{{ old('order_date', \Carbon\Carbon::parse($order->order_date)->format('Y-m-d')) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-orange-500/50" required>
                    @error('order_date') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Kilometer Saat Ini</label>
                    <input type="number" name="current_km" value="{{ old('current_km', $order->current_km) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-orange-500/50" required>
                    @error('current_km') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-400 mb-1">Keluhan Pelanggan</label>
                    <textarea name="complaint" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-orange-500/50" required>{{ old('complaint', $order->complaint) }}</textarea>
                    @error('complaint') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Pilih Layanan Servis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-2">
                @foreach($services as $srv)
                <label class="flex items-center gap-3 p-3 bg-gray-900 border border-gray-700 rounded-lg cursor-pointer hover:border-orange-500/50">
                    <input type="checkbox" name="services[]" value="{{ $srv->id }}" class="w-5 h-5 text-orange-500 rounded bg-gray-800 border-gray-600 focus:ring-orange-500/50" 
                        {{ in_array($srv->id, old('services', $selectedServiceIds)) ? 'checked' : '' }}>
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
                    <label class="block text-sm text-gray-400 mb-1">Tugaskan Mekanik</label>
                    <select name="mechanic_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-orange-500/50">
                        <option value="">-- Belum Ditugaskan --</option>
                        @foreach($mechanics as $mekanik)
                            <option value="{{ $mekanik->id }}" {{ old('mechanic_id', $order->mechanic_id) == $mekanik->id ? 'selected' : '' }}>{{ $mekanik->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Catatan Internal (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-orange-500/50">{{ old('notes', $order->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('service-orders.index') }}" class="px-5 py-3 text-gray-400 hover:text-gray-200">Batal</a>
            <button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
