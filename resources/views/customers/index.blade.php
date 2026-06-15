@extends('layouts.app')
@section('title', 'Data Pelanggan')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-100">Manajemen Pelanggan</h1>
        @can('manage-customers')
        <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow transition">
            + Tambah Pelanggan
        </a>
        @endcan
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">No HP</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse ($customers as $cust)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 font-medium text-gray-200">{{ $cust->customer_code }}</td>
                        <td class="px-4 py-3">{{ $cust->name }}</td>
                        <td class="px-4 py-3">{{ $cust->phone }}</td>
                        <td class="px-4 py-3">{{ $cust->email ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $cust->status === 'aktif' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                {{ strtoupper($cust->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('customers.show', $cust) }}" class="text-blue-400 hover:text-blue-300">Lihat</a>
                            @can('manage-customers')
                            <a href="{{ route('customers.edit', $cust) }}" class="text-yellow-400 hover:text-yellow-300">Edit</a>
                            @if($cust->status === 'aktif')
                            <form action="{{ route('customers.deactivate', $cust) }}" method="POST" onsubmit="return confirm('Nonaktifkan pelanggan ini?');">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-red-400 hover:text-red-300">Nonaktifkan</button>
                            </form>
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada data pelanggan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection