@extends('layouts.app')
@section('title', 'Input Hasil Pemeriksaan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('mechanic-jobs.show', $job) }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Input Hasil Pengerjaan</h1>
    </div>

    {{-- Info Order & Keluhan --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-300">
            <div><span class="block text-gray-500 mb-1">No Order:</span> <strong class="text-orange-400">{{ $job->serviceOrder->order_number }}</strong></div>
            <div><span class="block text-gray-500 mb-1">Kendaraan:</span> <strong>{{ $job->serviceOrder->vehicle->license_plate }} ({{ $job->serviceOrder->vehicle->brand }})</strong></div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-700">
            <span class="block text-gray-500 mb-1 text-sm">Keluhan Pelanggan:</span>
            <p class="text-gray-200 bg-gray-900/50 p-3 rounded-lg border border-gray-700">"{{ $job->serviceOrder->complaint }}"</p>
        </div>
    </div>

    {{-- Form Hasil --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
        <form action="{{ route('mechanic-jobs.update', $job) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Hasil Pemeriksaan *</label>
                <textarea name="inspection_result" rows="3" placeholder="Contoh: Kampas rem depan habis 80%, oli mesin sangat hitam" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-gray-100 focus:ring-2 focus:ring-orange-500" required>{{ old('inspection_result', $job->inspection_result) }}</textarea>
                @error('inspection_result') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Tindakan Servis yang Dilakukan *</label>
                <textarea name="service_actions" rows="3" placeholder="Contoh: Mengganti kampas rem depan, ganti oli mesin dan bersihkan filter" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-gray-100 focus:ring-2 focus:ring-orange-500" required>{{ old('service_actions', $job->service_actions) }}</textarea>
                @error('service_actions') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Catatan Tambahan Mekanik (Opsional)</label>
                <textarea name="mechanic_notes" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-gray-100 focus:ring-2 focus:ring-orange-500">{{ old('mechanic_notes', $job->mechanic_notes) }}</textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('mechanic-jobs.show', $job) }}" class="px-5 py-2 text-gray-400 hover:text-gray-200">Batal</a>
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg shadow-lg">Simpan Catatan Pengerjaan</button>
            </div>
        </form>
    </div>
</div>
@endsection