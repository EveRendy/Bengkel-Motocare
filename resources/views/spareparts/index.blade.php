@extends('layouts.app')
@section('title', 'Data Sparepart')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-100">Data Sparepart</h1>
        @can('manage-spareparts')
        <a href="{{ route('spareparts.create') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow">
            + Tambah Sparepart
        </a>
        @endcan
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="bg-gray-700/50 text-gray-400 uppercase">
                <tr>
                    <th class="px-4 py-3">Kode Part</th>
                    <th class="px-4 py-3">Nama Part</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Status Stok</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse ($spareparts as $sp)
                <tr class="hover:bg-gray-700/30 transition">
                    <td class="px-4 py-3 font-medium">{{ $sp->part_code }}</td>
                    <td class="px-4 py-3">{{ $sp->name }}</td>
                    <td class="px-4 py-3 font-bold text-orange-400">Rp {{ number_format($sp->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        @if($sp->stock_status === 'tersedia')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-500/10 text-green-400">Tersedia</span>
                        @elseif($sp->stock_status === 'habis')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-500/10 text-red-400">Habis</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-500/10 text-gray-400">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 flex gap-3">
                        <a href="{{ route('spareparts.edit', $sp) }}" class="text-yellow-400 hover:text-yellow-300">Edit</a>
                        
                        {{-- Quick Update Stok Form --}}
                        <form action="{{ route('spareparts.status', $sp) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="stock_status" value="{{ $sp->stock_status === 'tersedia' ? 'habis' : 'tersedia' }}">
                            <button type="submit" class="text-blue-400 hover:text-blue-300">
                                Tandai {{ $sp->stock_status === 'tersedia' ? 'Habis' : 'Tersedia' }}
                            </button>
                        </form>

                        {{-- Delete Form --}}
                        <form action="{{ route('spareparts.destroy', $sp) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" data-confirm-submit data-confirm-title="Hapus Sparepart" data-confirm-message="Yakin ingin menghapus sparepart ini?" data-confirm-text="Hapus" class="text-red-400 hover:text-red-300">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data sparepart.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-700">
            {{ $spareparts->links() }}
        </div>
    </div>
</div>
@endsection
