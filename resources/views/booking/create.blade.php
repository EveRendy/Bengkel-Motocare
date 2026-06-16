@extends('layouts.app')
@section('title', 'Buat Booking Servis')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Booking Servis Online</h1>
    </div>

    <div class="bg-blue-500/10 border border-blue-500/30 text-blue-400 p-4 rounded-xl flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <p class="text-sm">Isi formulir ini untuk melakukan pemesanan waktu servis. Setelah booking, bawa kendaraan Anda ke bengkel pada tanggal yang dipilih. Mekanik kami akan melakukan pengecekan dan menentukan layanan yang dibutuhkan.</p>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
        <form action="{{ route('booking.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Pilih Kendaraan Anda</label>
                @if($vehicles->count() > 0)
                <select name="vehicle_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-gray-100 focus:ring-2 focus:ring-orange-500/50" required>
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach($vehicles as $veh)
                        <option value="{{ $veh->id }}" {{ old('vehicle_id') == $veh->id ? 'selected' : '' }}>
                            {{ $veh->license_plate }} - {{ $veh->brand }} {{ $veh->type }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                @else
                <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg text-center text-gray-500">
                    Anda belum memiliki data kendaraan yang aktif.
                    <br><span class="text-sm">Silakan hubungi admin bengkel untuk mendaftarkan kendaraan Anda.</span>
                </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Tanggal Rencana Servis</label>
                <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-gray-100 focus:ring-2 focus:ring-orange-500/50" required>
                @error('order_date') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Kilometer Saat Ini (Angka saja)</label>
                <input type="number" name="current_km" value="{{ old('current_km', 0) }}" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-gray-100 focus:ring-2 focus:ring-orange-500/50" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Keluhan / Masalah Motor</label>
                <textarea name="complaint" rows="4" placeholder="Contoh: Rem depan blong, mesin brebet saat digas..." class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-gray-100 focus:ring-2 focus:ring-orange-500/50" required>{{ old('complaint') }}</textarea>
                @error('complaint') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5" {{ $vehicles->count() == 0 ? 'disabled' : '' }}>
                    Kirim Booking Servis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection