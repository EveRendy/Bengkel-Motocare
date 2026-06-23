@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $roleName = auth()->user()->roles->pluck('name')->first() ?? '-';
    $colorMap = [
        'blue' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
        'yellow' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
        'orange' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
        'green' => 'bg-green-500/10 text-green-400 border-green-500/20',
        'purple' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
    ];
@endphp

<div class="space-y-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Dashboard</h1>
            <p class="text-gray-400 mt-1">
                Selamat datang, <span class="text-orange-400 font-medium">{{ auth()->user()->name }}</span>
                <span class="text-gray-600">-</span>
                <span class="text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
            </p>
        </div>
        <span class="w-fit px-3 py-1 rounded-full bg-gray-800 border border-gray-700 text-sm text-gray-300">
            Role: {{ str_replace('_', ' ', strtoupper($roleName)) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        @foreach($cards as $card)
            @php $colorClass = $colorMap[$card['color']] ?? $colorMap['blue']; @endphp
            <div class="bg-gray-800/60 backdrop-blur-sm border border-gray-700/50 rounded-2xl p-6 hover:border-orange-500/30 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center border {{ $colorClass }}">
                        <span class="w-3 h-3 rounded-full bg-current"></span>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-100">{{ $card['value'] }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    @if(count($quickActions) > 0)
    <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-gray-200 mb-4">Akses Cepat</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($quickActions as $action)
                <a href="{{ route($action['route']) }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow text-sm font-medium">
                    {{ $action['label'] }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-700/50">
            <h2 class="text-lg font-semibold text-gray-200">{{ $activityTitle }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                <thead class="bg-gray-700/30 text-gray-400 uppercase">
                    <tr>
                        <th class="px-6 py-3">No Order</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Kendaraan</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-700/20">
                        <td class="px-6 py-4 font-medium text-orange-400">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">{{ $order->customer->name ?? auth()->user()->name }}</td>
                        <td class="px-6 py-4">{{ $order->vehicle->license_plate ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <x-ui.status-badge :status="$order->status" />
                        </td>
                        <td class="px-6 py-4">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada aktivitas order.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($topServices->isNotEmpty() || $topSpareparts->isNotEmpty() || $mechanicOrderStats->isNotEmpty())
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Layanan Terpopuler</h2>
            </div>
            <div class="divide-y divide-gray-700/40">
                @forelse($topServices as $service)
                <div class="px-5 py-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-gray-100 font-medium">{{ $service->name }}</p>
                        <p class="text-xs text-gray-500">{{ $service->service_code }}</p>
                    </div>
                    <span class="text-orange-400 font-bold">{{ $service->total_orders }}</span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-500">Belum ada layanan terpakai.</div>
                @endforelse
            </div>
        </section>

        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Sparepart Terpakai</h2>
            </div>
            <div class="divide-y divide-gray-700/40">
                @forelse($topSpareparts as $sparepart)
                <div class="px-5 py-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-gray-100 font-medium">{{ $sparepart->name }}</p>
                        <p class="text-xs text-gray-500">{{ $sparepart->part_code }}</p>
                    </div>
                    <span class="text-orange-400 font-bold">{{ $sparepart->total_quantity }}</span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-500">Belum ada sparepart terpakai.</div>
                @endforelse
            </div>
        </section>

        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Order per Mekanik</h2>
            </div>
            <div class="divide-y divide-gray-700/40">
                @forelse($mechanicOrderStats as $mechanic)
                <div class="px-5 py-4 flex items-center justify-between gap-4">
                    <p class="text-gray-100 font-medium">{{ $mechanic->name }}</p>
                    <span class="text-orange-400 font-bold">{{ $mechanic->total_orders }}</span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-500">Belum ada order mekanik.</div>
                @endforelse
            </div>
        </section>
    </div>
    @endif

    <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-gray-200 mb-3">Informasi Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center gap-3 text-sm text-gray-400">
                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                <span>Sistem berjalan normal</span>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-400">
                <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                <span>Akses menu disesuaikan dengan role login.</span>
            </div>
        </div>
    </div>
</div>
@endsection
