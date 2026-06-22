@extends('layouts.app')
@section('title', 'Tambah Kendaraan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('my-vehicles.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Tambah Kendaraan Saya</h1>
    </div>

    <div class="bg-blue-500/10 border border-blue-500/30 p-4 rounded-xl text-blue-400 text-sm">
        <strong>Informasi:</strong> Data kendaraan yang Anda masukkan akan diverifikasi oleh petugas bengkel saat Anda melakukan servis untuk pertama kalinya. Pastikan plat nomor sesuai dengan STNK.
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-sm">
        <form action="{{ route('my-vehicles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Plat Nomor *</label>
                <input type="text" name="license_plate" value="{{ old('license_plate') }}" placeholder="Contoh: B 1234 ABC" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 uppercase" required>
                @error('license_plate') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Merk *</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Contoh: Honda" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Tipe *</label>
                    <input type="text" name="type" value="{{ old('type') }}" placeholder="Contoh: Vario 150" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Tahun Kendaraan *</label>
                    <input type="number" name="year" value="{{ old('year') }}" placeholder="Contoh: 2020" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Warna *</label>
                    <input type="text" name="color" value="{{ old('color') }}" placeholder="Contoh: Hitam" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">No Rangka (Opsional)</label>
                    <input type="text" name="chassis_number" value="{{ old('chassis_number') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">No Mesin (Opsional)</label>
                    <input type="text" name="engine_number" value="{{ old('engine_number') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Kilometer Terakhir (KM) *</label>
                <input type="number" name="last_km" value="{{ old('last_km', 0) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Upload Foto Kendaraan (Opsional, Max 2MB)</label>
                <input type="file" name="photo" accept="image/*" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-400">
                @error('photo') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('my-vehicles.index') }}" class="px-5 py-2 text-gray-400 hover:text-gray-200">Batal</a>
                <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg shadow-lg">Simpan Kendaraan</button>
            </div>
        </form>
    </div>
</div>
@endsection