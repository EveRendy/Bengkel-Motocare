@extends('layouts.app')
@section('title', 'Tambah Layanan Servis')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('services.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Tambah Layanan Baru</h1>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
        <form action="{{ route('services.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm text-gray-400 mb-1">Kategori Layanan</label>
                <select name="category_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(\App\Models\ServiceCategory::all() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nama Layanan</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-1">Harga (Rp)</label>
                <input type="number" name="price" value="{{ old('price', 0) }}" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 font-bold text-orange-400" required>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow w-full">Simpan Layanan</button>
            </div>
        </form>
    </div>
</div>
@endsection