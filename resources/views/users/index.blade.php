@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-100">Manajemen User Internal</h1>
        <a href="{{ route('users.create') }}" class="w-fit px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow transition">
            + Tambah User
        </a>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 font-medium text-gray-100">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->roles->pluck('name')->map(fn($role) => str_replace('_', ' ', strtoupper($role)))->join(', ') ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->status === 'aktif' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                {{ strtoupper($user->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('users.edit', $user) }}" class="text-yellow-400 hover:text-yellow-300">Edit</a>
                                @if($user->status === 'aktif' && $user->id !== auth()->id())
                                <form action="{{ route('users.deactivate', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button" data-confirm-submit data-confirm-title="Nonaktifkan User" data-confirm-message="Nonaktifkan user ini?" data-confirm-text="Nonaktifkan" class="text-red-400 hover:text-red-300">Nonaktifkan</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
