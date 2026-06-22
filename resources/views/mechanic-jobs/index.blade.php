@extends('layouts.app')
@section('title', auth()->user()->hasRole('mekanik') ? 'Pekerjaan Saya' : 'Monitoring Pengerjaan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-100">{{ auth()->user()->hasRole('mekanik') ? 'Daftar Pekerjaan Saya' : 'Monitoring Pengerjaan Mekanik' }}</h1>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl">{{ session('error') }}</div>
    @endif

    {{-- Filter untuk Admin/SA --}}
    @if(!auth()->user()->hasRole('mekanik') && count($mechanics) > 0)
    <div class="bg-gray-800 p-4 border border-gray-700 rounded-xl shadow-sm">
        <form method="GET" action="{{ route('mechanic-jobs.index') }}" class="flex items-end gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Filter Mekanik</label>
                <select name="mechanic_id" class="bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" onchange="this.form.submit()">
                    <option value="">Semua Mekanik</option>
                    @foreach($mechanics as $mekanik)
                        <option value="{{ $mekanik->id }}" {{ request('mechanic_id') == $mekanik->id ? 'selected' : '' }}>{{ $mekanik->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    @endif

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                <thead class="bg-gray-700/50 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">No Order</th>
                        <th class="px-4 py-3">Kendaraan</th>
                        @if(!auth()->user()->hasRole('mekanik'))
                        <th class="px-4 py-3">Mekanik</th>
                        @endif
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Waktu Mulai</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse ($jobs as $job)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 font-medium text-orange-400">{{ $job->serviceOrder->order_number }}</td>
                        <td class="px-4 py-3">{{ $job->serviceOrder->vehicle->license_plate }} ({{ $job->serviceOrder->vehicle->brand }})</td>
                        @if(!auth()->user()->hasRole('mekanik'))
                        <td class="px-4 py-3">{{ $job->mechanic->name }}</td>
                        @endif
                        <td class="px-4 py-3">
                            @php
                                $status = $job->serviceOrder->status;
                                $badgeColor = match($status) {
                                    'menunggu' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                    'dikerjakan' => 'bg-orange-500/10 text-orange-400 border border-orange-500/20',
                                    'selesai_servis' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                                    default => 'bg-gray-500/10 text-gray-400 border border-gray-500/20'
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $badgeColor }}">
                                {{ strtoupper(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $job->started_at ? \Carbon\Carbon::parse($job->started_at)->format('d/m/Y H:i') : 'Belum Dimulai' }}</td>
                        <td class="px-4 py-3 text-right flex justify-end gap-2">
                            <a href="{{ route('mechanic-jobs.show', $job) }}" class="text-blue-400 hover:text-blue-300 bg-blue-400/10 px-3 py-1 rounded border border-blue-400/20 text-xs">Detail</a>
                            
                            {{-- AKSI UNTUK MEKANIK --}}
                            @if(auth()->user()->hasRole('mekanik') && auth()->id() === $job->mechanic_id)
                                @if($job->serviceOrder->status === 'menunggu')
                                    <form action="{{ route('mechanic-jobs.start', $job) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-white hover:text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded shadow text-xs transition">Mulai Kerjakan</button>
                                    </form>
                                @elseif($job->serviceOrder->status === 'dikerjakan')
                                    <a href="{{ route('mechanic-jobs.edit', $job) }}" class="text-white hover:text-white bg-orange-600 hover:bg-orange-700 px-3 py-1 rounded shadow text-xs transition">Input Hasil</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada pekerjaan yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection
