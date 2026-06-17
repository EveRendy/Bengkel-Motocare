@extends('layouts.app')
@section('title', 'Ubah Data Kendaraan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('my-vehicles.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Ubah Kendaraan: {{ $vehicle->license_plate }}</h1>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
        <form action="{{ route('my-vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Plat Nomor <span class="text-xs text-red-400 ml-2">(Tidak bisa diubah)</span></label>
                <input type="text" value="{{ $vehicle->license_plate }}" class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-gray-500 cursor-not-allowed" disabled readonly>
                <p class="text-xs text-gray-500 mt-1">Plat nomor tidak dapat diubah. Hubungi admin bengkel jika terjadi kesalahan input.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Merk *</label>
                    <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Tipe *</label>
                    <input type="text" name="type" value="{{ old('type', $vehicle->type) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Tahun Kendaraan *</label>
                    <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Warna *</label>
                    <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">No Rangka</label>
                    <input type="text" name="chassis_number" value="{{ old('chassis_number', $vehicle->chassis_number) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">No Mesin</label>
                    <input type="text" name="engine_number" value="{{ old('engine_number', $vehicle->engine_number) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Kilometer Terakhir</label>
                <input type="number" name="last_km" value="{{ old('last_km', $vehicle->last_km) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Upload Foto Baru (Max 2MB)</label>
                @if($vehicle->fileUploads->count() > 0)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $vehicle->fileUploads->first()->file_path) }}" alt="Current Photo" class="w-24 h-24 object-cover rounded shadow border border-gray-600">
                        <p class="text-xs text-gray-500 mt-1">Upload file baru untuk mengganti foto ini.</p>
                    </div>
                @endif
                <input type="file" name="photo" accept="image/*" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-400">
                @error('photo') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">{{ old('notes', $vehicle->notes) }}</textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('my-vehicles.index') }}" class="px-5 py-2 text-gray-400 hover:text-gray-200">Batal</a>
                <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg shadow-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection