@extends('layouts.app')
@section('title', 'Proses Booking')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('bookings.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Proses Booking Menjadi Order</h1>
    </div>

    @if(session('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl">{{ session('error') }}</div>
    @endif

    <form action="{{ route('bookings.process', $order) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- KOLOM KIRI: Data Basic & Penugasan --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Informasi Booking</h2>
                    <div class="space-y-3 text-sm">
                        <div><span class="block text-gray-500">No. Booking</span><span class="text-orange-400 font-bold">{{ $order->order_number ?? '-' }}</span></div>
                        <div><span class="block text-gray-500">Pelanggan</span><span class="text-gray-200">{{ $order->customer->name }}</span></div>
                        <div><span class="block text-gray-500">Kendaraan</span><span class="text-gray-200">{{ $order->vehicle->license_plate }} ({{ $order->vehicle->brand }})</span></div>
                        <div><span class="block text-gray-500">Tgl Diajukan</span><span class="text-gray-200">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</span></div>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Penugasan & Catatan</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Pilih Mekanik (Opsional)</label>
                            <select name="mechanic_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Kosongkan (Isi Nanti) --</option>
                                @foreach($mechanics as $mekanik)
                                    <option value="{{ $mekanik->id }}" {{ old('mechanic_id') == $mekanik->id ? 'selected' : '' }}>{{ $mekanik->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih jika mekanik sudah siap bekerja saat ini juga.</p>
                            @error('mechanic_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Catatan Internal</label>
                            <textarea name="notes" rows="3" placeholder="Catatan untuk Kasir/SA lain..." class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                            @error('notes') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Form Keluhan & Pilih Layanan --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Detail Servis</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Keluhan / Permintaan Pelanggan *</label>
                            <textarea name="complaint" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-blue-500" required>{{ old('complaint', $order->complaint) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Anda bisa memperjelas bahasa keluhan dari pelanggan di sini.</p>
                            @error('complaint') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Kilometer Saat Ini *</label>
                            <input type="number" name="current_km" value="{{ old('current_km', $order->current_km) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-blue-500" required>
                            @error('current_km') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Pilih Layanan *</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[300px] overflow-y-auto pr-2 mb-4">
                        @foreach($services as $srv)
                        <label class="flex items-center gap-3 p-3 bg-gray-900 border border-gray-700 rounded-lg cursor-pointer hover:border-blue-500 transition service-label">
                            <input type="checkbox" name="services[]" value="{{ $srv->id }}" data-price="{{ $srv->price }}" class="w-5 h-5 text-blue-500 rounded bg-gray-800 border-gray-600 focus:ring-blue-500 service-checkbox" 
                                {{ in_array($srv->id, old('services', [])) ? 'checked' : '' }}>
                            <div class="flex-1 flex justify-between items-center">
                                <div class="text-sm">
                                    <span class="block text-gray-200">{{ $srv->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $srv->category->name ?? 'Layanan' }}</span>
                                </div>
                                <span class="text-orange-400 text-sm font-bold">Rp {{ number_format($srv->price, 0, ',', '.') }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('services') <span class="text-red-400 text-xs mt-1 block mb-3">{{ $message }}</span> @enderror
                    
                    <div class="p-4 bg-gray-900 rounded-lg border border-gray-700 flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Total Estimasi Biaya:</span>
                        <span id="total-estimasi" class="text-2xl font-bold text-green-400">Rp 0</span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('bookings.index') }}" class="px-6 py-3 text-gray-400 hover:text-gray-200">Batal</a>
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">Simpan Sebagai Order</button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- JS Auto hitung estimasi dan highlight checkbox --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.service-checkbox');
        const totalDisplay = document.getElementById('total-estimasi');

        function updateTotal() {
            let total = 0;
            checkboxes.forEach(cb => {
                if(cb.checked) {
                    total += parseFloat(cb.dataset.price);
                    cb.closest('label').classList.add('border-blue-500', 'bg-blue-900/20');
                    cb.closest('label').classList.remove('border-gray-700', 'bg-gray-900');
                } else {
                    cb.closest('label').classList.remove('border-blue-500', 'bg-blue-900/20');
                    cb.closest('label').classList.add('border-gray-700', 'bg-gray-900');
                }
            });
            
            totalDisplay.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency', 
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(total);
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateTotal));
        updateTotal(); // Hitung nilai awal (berguna saat validasi error / ada input old)
    });
</script>
@endsection