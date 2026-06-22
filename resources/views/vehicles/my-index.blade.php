@extends('layouts.app')
@section('title', 'Kendaraan Saya')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-100">Kendaraan Saya</h1>
        <a href="{{ route('my-vehicles.create') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow whitespace-nowrap">
            + Tambah Kendaraan
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Plat Nomor</th>
                        <th class="px-4 py-3">Merk & Tipe</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Warna</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse ($vehicles as $veh)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3">
                            @if($veh->fileUploads->count() > 0)
                                <img src="{{ asset('storage/' . $veh->fileUploads->first()->file_path) }}" alt="Foto Motor" class="w-12 h-12 object-cover rounded shadow">
                            @else
                                <div class="w-12 h-12 bg-gray-700 rounded flex items-center justify-center text-xs text-gray-500 shadow">No Pic</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-orange-400">{{ $veh->license_plate }}</td>
                        <td class="px-4 py-3">{{ $veh->brand }} {{ $veh->type }}</td>
                        <td class="px-4 py-3">{{ $veh->year }}</td>
                        <td class="px-4 py-3">{{ $veh->color }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $veh->status == 'aktif' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                {{ ucfirst($veh->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right flex justify-end gap-2">
                            <a href="{{ route('my-vehicles.show', $veh) }}" class="text-blue-400 hover:text-blue-300 bg-blue-400/10 px-3 py-1 rounded">Lihat</a>
                            <a href="{{ route('my-vehicles.edit', $veh) }}" class="text-yellow-400 hover:text-yellow-300 bg-yellow-400/10 px-3 py-1 rounded">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Anda belum mendaftarkan kendaraan. <br>
                            <span class="text-sm">Klik tombol di atas untuk menambah.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700">
            {{ $vehicles->links() }}
        </div>
    </div>
</div>
@endsection