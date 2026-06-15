@extends('layouts.app')
@section('title', 'Detail Pelanggan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        @can('manage-customers')
        <a href="{{ route('customers.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
        @endcan
        <h1 class="text-2xl font-bold text-gray-100">Detail Pelanggan</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info Pelanggan --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 h-fit">
            <h2 class="text-lg font-semibold text-white mb-4">Profil</h2>
            <div class="space-y-3 text-sm">
                <div><span class="text-gray-500">Kode:</span> <span class="text-gray-200 block">{{ $customer->customer_code }}</span></div>
                <div><span class="text-gray-500">Nama:</span> <span class="text-gray-200 block">{{ $customer->name }}</span></div>
                <div><span class="text-gray-500">No HP:</span> <span class="text-gray-200 block">{{ $customer->phone }}</span></div>
                <div><span class="text-gray-500">Email:</span> <span class="text-gray-200 block">{{ $customer->email ?? '-' }}</span></div>
                <div><span class="text-gray-500">Alamat:</span> <span class="text-gray-200 block">{{ $customer->address }}</span></div>
                <div><span class="text-gray-500">Bergabung:</span> <span class="text-gray-200 block">{{ \Carbon\Carbon::parse($customer->registration_date)->format('d M Y') }}</span></div>
                <div>
                    <span class="text-gray-500">Status:</span> 
                    <span class="mt-1 inline-block px-2 py-1 text-xs rounded-full {{ $customer->status === 'aktif' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                        {{ strtoupper($customer->status) }}
                    </span>
                </div>
            </div>
            @can('manage-customers')
            <div class="mt-6 pt-4 border-t border-gray-700">
                <a href="{{ route('customers.edit', $customer) }}" class="block w-full text-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">Edit Profil</a>
            </div>
            @endcan
        </div>

        {{-- Kendaraan & Riwayat --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Daftar Kendaraan --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="p-4 border-b border-gray-700 bg-gray-800/50 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-white">Kendaraan Milik Pelanggan</h2>
                </div>
                <div class="p-4">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead class="text-gray-400 border-b border-gray-700">
                            <tr>
                                <th class="pb-2">Plat Nomor</th>
                                <th class="pb-2">Merk/Tipe</th>
                                <th class="pb-2">Tahun</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @forelse ($customer->vehicles as $vehicle)
                            <tr class="hover:bg-gray-700/30">
                                <td class="py-3">{{ $vehicle->license_plate }}</td>
                                <td class="py-3">{{ $vehicle->brand }} {{ $vehicle->type }}</td>
                                <td class="py-3">{{ $vehicle->year ?? '-' }}</td>
                                <td class="py-3">{{ $vehicle->status }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">Belum ada kendaraan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Riwayat Servis --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Riwayat Servis</h2>
                <div class="p-8 text-center text-gray-500 bg-gray-900/50 rounded-lg border border-gray-700 border-dashed">
                    Riwayat servis akan ditampilkan di sini pada tahap selanjutnya.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection