@extends('layouts.app')
@section('title', 'Edit Kendaraan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('vehicles.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Edit Kendaraan: {{ $vehicle->license_plate }}</h1>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Plat Nomor</label>
                    <input type="text" name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 uppercase" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Status</label>
                    <select name="status" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                        <option value="aktif" {{ $vehicle->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $vehicle->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Merk Motor</label>
                    <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tipe Motor</label>
                    <input type="text" name="type" value="{{ old('type', $vehicle->type) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm text-gray-400 mb-1">Tahun</label>
                        <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm text-gray-400 mb-1">Warna</label>
                        <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">No Rangka</label>
                    <input type="text" name="chassis_number" value="{{ old('chassis_number', $vehicle->chassis_number) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">No Mesin</label>
                    <input type="text" name="engine_number" value="{{ old('engine_number', $vehicle->engine_number) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Kilometer Saat Ini</label>
                <input type="number" name="last_km" value="{{ old('last_km', $vehicle->last_km) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Catatan</label>
                <textarea name="notes" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">{{ old('notes', $vehicle->notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-gray-700">
                <label class="block text-sm text-gray-400 mb-2">Foto Kendaraan</label>
                @if($vehicle->fileUploads->count() > 0)
                    <div class="mb-4">
                        <img src="{{ Storage::url($vehicle->fileUploads->first()->path) }}" alt="Foto Motor" class="h-32 rounded-lg border border-gray-600">
                    </div>
                @endif
                <input type="file" name="photo" accept="image/jpeg,image/png" class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-gray-100 hover:file:bg-gray-600">
                <p class="text-xs text-gray-500 mt-1">*Upload foto baru akan menggantikan foto lama</p>
            </div>

            <div class="pt-4">
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow">Update Kendaraan</button>
            </div>
        </form>
    </div>
</div>
@endsection