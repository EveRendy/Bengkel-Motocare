@extends('layouts.app')
@section('title', 'Detail Kendaraan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        @can('manage-vehicles')
        <a href="{{ route('vehicles.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        @endcan
        <h1 class="text-2xl font-bold text-gray-100">Detail Kendaraan</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden h-fit">
            @if($vehicle->fileUploads->count() > 0)
                <img src="{{ Storage::url($vehicle->fileUploads->first()->path) }}" alt="Foto Motor" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-900 flex items-center justify-center text-gray-600">Tidak ada foto</div>
            @endif
            <div class="p-6">
                <h2 class="text-2xl font-bold text-orange-400 mb-1">{{ $vehicle->license_plate }}</h2>
                <p class="text-gray-400 text-sm mb-4">{{ $vehicle->brand }} {{ $vehicle->type }} ({{ $vehicle->year }})</p>
                
                <div class="space-y-2 text-sm border-t border-gray-700 pt-4">
                    <div class="flex justify-between"><span class="text-gray-500">Pemilik</span> <span class="text-gray-200">{{ $vehicle->customer->name ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Warna</span> <span class="text-gray-200">{{ $vehicle->color }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">No Rangka</span> <span class="text-gray-200">{{ $vehicle->chassis_number ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">No Mesin</span> <span class="text-gray-200">{{ $vehicle->engine_number ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Kilometer</span> <span class="text-gray-200">{{ number_format($vehicle->last_km, 0, ',', '.') }} KM</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Status</span> 
                        <span class="{{ $vehicle->status === 'aktif' ? 'text-green-400' : 'text-red-400' }}">{{ strtoupper($vehicle->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Riwayat Servis</h2>
                <div class="p-8 text-center text-gray-500 bg-gray-900/50 rounded-lg border border-gray-700 border-dashed">
                    Riwayat servis kendaraan ini akan ditampilkan di sini.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection