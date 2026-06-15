@extends('layouts.app')
@section('title', 'Tambah Sparepart')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('spareparts.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Tambah Sparepart Baru</h1>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
        <form action="{{ route('spareparts.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nama Sparepart</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Harga (Rp)</label>
                <input type="number" name="price" value="{{ old('price', 0) }}" min="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 font-bold text-orange-400" required>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow">Simpan Sparepart</button>
            </div>
        </form>
    </div>
</div>
@endsection