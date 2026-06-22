@extends('layouts.app')
@section('title', 'Data Kendaraan')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-100">Manajemen Kendaraan</h1>
        @can('manage-vehicles')
        <a href="{{ route('vehicles.create') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow">
            + Tambah Kendaraan
        </a>
        @endcan
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="bg-gray-700/50 text-gray-400 uppercase">
                <tr>
                    <th class="px-4 py-3">Plat Nomor</th>
                    <th class="px-4 py-3">Merk & Tipe</th>
                    <th class="px-4 py-3">Pemilik</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse ($vehicles as $veh)
                <tr class="hover:bg-gray-700/30 transition">
                    <td class="px-4 py-3 font-bold text-orange-400">{{ $veh->license_plate }}</td>
                    <td class="px-4 py-3">{{ $veh->brand }} {{ $veh->type }} ({{ $veh->year }})</td>
                    <td class="px-4 py-3">{{ $veh->customer->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $veh->status === 'aktif' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                            {{ strtoupper($veh->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('vehicles.show', $veh) }}" class="text-blue-400 hover:text-blue-300">Lihat</a>
                        @can('manage-vehicles')
                        <a href="{{ route('vehicles.edit', $veh) }}" class="text-yellow-400 hover:text-yellow-300">Edit</a>
                        @if($veh->status === 'aktif')
                        <form action="{{ route('vehicles.deactivate', $veh) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="button" data-confirm-submit data-confirm-title="Nonaktifkan Kendaraan" data-confirm-message="Nonaktifkan kendaraan ini?" data-confirm-text="Nonaktifkan" class="text-red-400 hover:text-red-300">Nonaktifkan</button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data kendaraan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-700">
            {{ $vehicles->links() }}
        </div>
    </div>
</div>
@endsection
