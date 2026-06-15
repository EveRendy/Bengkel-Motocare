@extends('layouts.app')
@section('title', 'Tambah Kendaraan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('vehicles.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Tambah Kendaraan</h1>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
        <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1">Pilih Pemilik (Pelanggan)</label>
                <select name="customer_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->customer_code }} - {{ $cust->name }}</option>
                    @endforeach
                </select>
                @error('customer_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Plat Nomor (Tanpa Spasi disarankan)</label>
                    <input type="text" name="license_plate" value="{{ old('license_plate') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 uppercase" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Merk Motor (Cth: Honda, Yamaha)</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tipe Motor (Cth: Vario 150)</label>
                    <input type="text" name="type" value="{{ old('type') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm text-gray-400 mb-1">Tahun</label>
                        <input type="number" name="year" value="{{ old('year', date('Y')) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm text-gray-400 mb-1">Warna</label>
                        <input type="text" name="color" value="{{ old('color') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">No Rangka (Opsional)</label>
                    <input type="text" name="chassis_number" value="{{ old('chassis_number') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">No Mesin (Opsional)</label>
                    <input type="text" name="engine_number" value="{{ old('engine_number') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Kilometer Saat Ini</label>
                <input type="number" name="last_km" value="{{ old('last_km', 0) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Upload Foto Kendaraan (Opsional)</label>
                <input type="file" name="photo" accept="image/jpeg,image/png" class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-gray-100 hover:file:bg-gray-600 cursor-pointer">
            </div>

            <div class="pt-4">
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow">Simpan Kendaraan</button>
            </div>
        </form>
    </div>
</div>
@endsection