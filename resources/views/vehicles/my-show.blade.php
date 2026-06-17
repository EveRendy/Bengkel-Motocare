@extends('layouts.app')
@section('title', 'Detail Kendaraan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('my-vehicles.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
            <h1 class="text-2xl font-bold text-gray-100">{{ $vehicle->license_plate }}</h1>
        </div>
        <a href="{{ route('my-vehicles.edit', $vehicle) }}" class="px-4 py-2 bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 border border-yellow-500/20 rounded-lg shadow">Edit Data</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex flex-col items-center">
                @if($vehicle->fileUploads->count() > 0)
                    <img src="{{ asset('storage/' . $vehicle->fileUploads->first()->file_path) }}" alt="Foto Motor" class="w-full aspect-square object-cover rounded-lg shadow-md mb-4 border border-gray-700">
                @else
                    <div class="w-full aspect-square bg-gray-900 border border-gray-700 rounded-lg flex flex-col items-center justify-center text-gray-500 mb-4 shadow-inner">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Belum Ada Foto</span>
                    </div>
                @endif
                <div class="text-center w-full">
                    <div class="text-2xl font-bold text-orange-400 tracking-wider mb-1">{{ $vehicle->license_plate }}</div>
                    <div class="text-gray-300">{{ $vehicle->brand }} {{ $vehicle->type }}</div>
                    <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full {{ $vehicle->status == 'aktif' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                        Status: {{ ucfirst($vehicle->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Spesifikasi Detail</h2>
                <div class="grid grid-cols-2 gap-y-4 text-sm">
                    <div><span class="block text-gray-500">Tahun Kendaraan</span><span class="text-gray-200 font-medium">{{ $vehicle->year }}</span></div>
                    <div><span class="block text-gray-500">Warna</span><span class="text-gray-200 font-medium">{{ $vehicle->color }}</span></div>
                    <div><span class="block text-gray-500">No Rangka</span><span class="text-gray-200 font-medium">{{ $vehicle->chassis_number ?: '-' }}</span></div>
                    <div><span class="block text-gray-500">No Mesin</span><span class="text-gray-200 font-medium">{{ $vehicle->engine_number ?: '-' }}</span></div>
                    <div><span class="block text-gray-500">Kilometer Terakhir</span><span class="text-gray-200 font-medium">{{ number_format($vehicle->last_km, 0, ',', '.') }} KM</span></div>
                    <div><span class="block text-gray-500">Ditambahkan Pada</span><span class="text-gray-200 font-medium">{{ $vehicle->created_at->format('d M Y') }}</span></div>
                </div>
                @if($vehicle->notes)
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <span class="block text-gray-500 text-sm mb-1">Catatan Tambahan:</span>
                    <p class="text-gray-300 text-sm bg-gray-900/50 p-3 rounded-lg">{{ $vehicle->notes }}</p>
                </div>
                @endif
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Riwayat Servis</h2>
                <div class="p-8 text-center border-2 border-dashed border-gray-700 rounded-xl bg-gray-900/30">
                    <p class="text-gray-400">Riwayat servis akan ditampilkan di sini setelah kendaraan pernah diservis di MotoCare.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection