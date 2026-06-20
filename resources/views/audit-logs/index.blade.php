@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Audit Log</h1>
            <p class="text-gray-400 mt-1">Riwayat aktivitas user pada sistem MotoCare.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="w-fit px-4 py-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-200 rounded-lg text-sm">
            Kembali ke Dashboard
        </a>
    </div>

    <form method="GET" action="{{ route('audit-logs.index') }}" class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-2xl p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-2">User</label>
                <select name="user_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-100">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Modul</label>
                <select name="module" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-100">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $module)
                    <option value="{{ $module }}" @selected(request('module') === $module)>{{ ucfirst(str_replace('_', ' ', $module)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-100">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-100">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Aktivitas</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..." class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-100">
            </div>
        </div>
        <div class="flex flex-wrap gap-3 mt-4">
            <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium">Filter</button>
            <a href="{{ route('audit-logs.index') }}" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-gray-300 rounded-lg text-sm">Reset</a>
        </div>
    </form>

    <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-700/50">
            <h2 class="text-lg font-semibold text-gray-200">Daftar Aktivitas</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                <thead class="bg-gray-700/30 text-gray-400 uppercase">
                    <tr>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Modul</th>
                        <th class="px-6 py-3">Aktivitas</th>
                        <th class="px-6 py-3">Data ID</th>
                        <th class="px-6 py-3">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-700/20">
                        <td class="px-6 py-4">{{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $log->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full bg-gray-900 border border-gray-700 text-xs text-gray-300">
                                {{ ucfirst(str_replace('_', ' ', $log->module)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-100">{{ $log->activity }}</td>
                        <td class="px-6 py-4">{{ $log->data_id ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada audit log yang sesuai filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700/50">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
