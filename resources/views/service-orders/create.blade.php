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
            <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Tipe Pelanggan</h2>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="customer_type" value="registered" id="customer_type_registered" class="w-5 h-5 text-orange-500 bg-gray-800 border-gray-600 focus:ring-orange-500/50" {{ old('customer_type', 'registered') == 'registered' ? 'checked' : '' }}>
                    <span class="text-gray-200">Pelanggan Terdaftar</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="customer_type" value="walkin" id="customer_type_walkin" class="w-5 h-5 text-orange-500 bg-gray-800 border-gray-600 focus:ring-orange-500/50" {{ old('customer_type') == 'walkin' ? 'checked' : '' }}>
                    <span class="text-gray-200">Pelanggan Langsung (Walk-in)</span>
                </label>
            </div>
        </div>

        <div id="registered_customer_fields" class="bg-gray-800 border border-gray-700 rounded-xl p-6 {{ old('customer_type') == 'walkin' ? 'hidden' : '' }}">
            <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Informasi Kendaraan & Pelanggan Terdaftar</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Pelanggan</label>
                    <select name="customer_id" id="customer_select" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }} ({{ $cust->customer_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Kendaraan</label>
                    <select name="vehicle_id" id="vehicle_select" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($vehicles as $veh)
                            <option value="{{ $veh->id }}" data-customer="{{ $veh->customer_id }}" {{ old('vehicle_id') == $veh->id ? 'selected' : '' }}>{{ $veh->license_plate }} - {{ $veh->customer->name ?? '' }}</option>
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

        <div id="walkin_customer_fields" class="bg-gray-800 border border-gray-700 rounded-xl p-6 {{ old('customer_type') == 'walkin' ? '' : 'hidden' }}">
            <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Informasi Pelanggan Langsung</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Nama Pelanggan</label>
                    <input type="text" name="walkin_customer_name" value="{{ old('walkin_customer_name') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Nama pelanggan">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Nomor Telepon</label>
                    <input type="text" name="walkin_customer_phone" value="{{ old('walkin_customer_phone') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Nomor HP">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Email (Opsional)</label>
                    <input type="email" name="walkin_customer_email" value="{{ old('walkin_customer_email') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Email">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Alamat (Opsional)</label>
                    <input type="text" name="walkin_customer_address" value="{{ old('walkin_customer_address') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Alamat">
                </div>
                <div class="md:col-span-2 border-t border-gray-700 pt-4 mt-2">
                    <h3 class="text-sm font-semibold text-gray-300 mb-3">Informasi Kendaraan</h3>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Plat Nomor</label>
                    <input type="text" name="walkin_vehicle_license_plate" value="{{ old('walkin_vehicle_license_plate') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Contoh: B 1234 ABC">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Merek</label>
                    <input type="text" name="walkin_vehicle_brand" value="{{ old('walkin_vehicle_brand') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Contoh: Yamaha">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tipe</label>
                    <input type="text" name="walkin_vehicle_type" value="{{ old('walkin_vehicle_type') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Contoh: NMAX">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tahun</label>
                    <input type="number" name="walkin_vehicle_year" value="{{ old('walkin_vehicle_year', date('Y')) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="2024">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Warna</label>
                    <input type="text" name="walkin_vehicle_color" value="{{ old('walkin_vehicle_color') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Warna kendaraan">
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

<script>
    // Toggle customer type
    document.querySelectorAll('input[name="customer_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const registeredFields = document.getElementById('registered_customer_fields');
            const walkinFields = document.getElementById('walkin_customer_fields');
            if (this.value === 'registered') {
                registeredFields.classList.remove('hidden');
                walkinFields.classList.add('hidden');
            } else {
                registeredFields.classList.add('hidden');
                walkinFields.classList.remove('hidden');
            }
        });
    });

    // Filter vehicles by selected customer
    document.getElementById('customer_select')?.addEventListener('change', function() {
        const customerId = this.value;
        const vehicleSelect = document.getElementById('vehicle_select');
        const options = vehicleSelect.querySelectorAll('option');
        
        options.forEach(opt => {
            if (opt.value === '') return; // Show default option always
            if (customerId === '' || opt.getAttribute('data-customer') === customerId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
                opt.selected = false;
            }
        });
    });
</script>
@endsection