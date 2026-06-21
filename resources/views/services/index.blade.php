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
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('services.edit', $srv) }}" class="text-yellow-400 hover:text-yellow-300 text-sm">Edit</a>
                                        
                                        {{-- Tombol Pemicu Modal Hapus --}}
                                        <button type="button" 
                                                onclick="openDeleteModal('{{ route('services.destroy', $srv) }}', '{{ $srv->name }}')"
                                                class="text-red-400 hover:text-red-300 text-sm bg-transparent border-0 p-0 cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
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

{{-- MODAL KONFIRMASI HAPUS --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 max-w-sm w-full shadow-xl transform transition-all scale-95 opacity-0 duration-200" id="modalContainer">
        <div class="text-center">
            {{-- Icon Peringatan --}}
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-900/30 border border-red-500 mb-4">
                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-lg font-bold text-gray-100">Hapus Layanan?</h3>
            <p class="text-sm text-gray-400 mt-2">
                Apakah Anda yakin ingin menghapus layanan <span id="deleteTargetName" class="font-semibold text-gray-200"></span>? Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        
        <div class="mt-6 flex space-x-3">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg text-sm font-medium transition">
                Batal
            </button>
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-lg shadow-red-600/20">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT MODAL --}}
<script>
    function openDeleteModal(actionUrl, serviceName) {
        const modal = document.getElementById('deleteModal');
        const container = document.getElementById('modalContainer');
        const form = document.getElementById('deleteForm');
        const nameSpan = document.getElementById('deleteTargetName');
        
        form.action = actionUrl;
        nameSpan.innerText = serviceName;
        
        // Tampilkan modal
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        const container = document.getElementById('modalContainer');
        
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Menutup modal jika user mengklik area gelap di luar modal box
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endsection