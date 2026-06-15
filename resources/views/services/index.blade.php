@extends('layouts.app')
@section('title', 'Layanan Servis')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-100">Layanan Servis & Kategori</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kategori Servis (Kiri) --}}
        <div class="space-y-4">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-200 mb-4">Kategori Layanan</h2>
                <ul class="space-y-2 mb-4">
                    @foreach($categories as $cat)
                        <li class="bg-gray-900 px-3 py-2 rounded-lg text-gray-300 border border-gray-700 text-sm">
                            {{ $cat->name }}
                        </li>
                    @endforeach
                </ul>
                
                {{-- Form Tambah Kategori --}}
                <form action="{{ route('service-categories.store') }}" method="POST" class="border-t border-gray-700 pt-4">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Kategori Baru" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-100 text-sm mb-2" required>
                    <button type="submit" class="w-full py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm transition">Tambah Kategori</button>
                </form>
            </div>
        </div>

        {{-- Daftar Layanan (Kanan) --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-200">Daftar Layanan</h2>
                    <a href="{{ route('services.create') }}" class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg shadow">
                        + Tambah Layanan
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead class="bg-gray-700/50 text-gray-400">
                            <tr>
                                <th class="px-4 py-2">Kode</th>
                                <th class="px-4 py-2">Nama Layanan</th>
                                <th class="px-4 py-2">Kategori</th>
                                <th class="px-4 py-2">Harga</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @forelse ($services as $srv)
                            <tr class="hover:bg-gray-700/30">
                                <td class="px-4 py-2">{{ $srv->service_code }}</td>
                                <td class="px-4 py-2">{{ $srv->name }}</td>
                                <td class="px-4 py-2">{{ $srv->category->name ?? '-' }}</td>
                                <td class="px-4 py-2 font-medium text-orange-400">Rp {{ number_format($srv->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('services.edit', $srv) }}" class="text-yellow-400 hover:text-yellow-300">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Belum ada layanan servis.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $services->links() }}</div>
            </div>
        </div>
        
    </div>
</div>
@endsection