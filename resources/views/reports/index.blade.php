@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
@php
    $cardColors = [
        'blue' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
        'green' => 'bg-green-500/10 text-green-400 border-green-500/20',
        'orange' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
        'purple' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
        'yellow' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
    ];
    $summaryCards = [
        ['label' => 'Total Pelanggan', 'value' => $summary['customers'], 'color' => 'blue'],
        ['label' => 'Total Kendaraan', 'value' => $summary['vehicles'], 'color' => 'green'],
        ['label' => 'Order Periode Ini', 'value' => $summary['orders'], 'color' => 'orange'],
        ['label' => 'Order Selesai', 'value' => $summary['completed_orders'], 'color' => 'green'],
        ['label' => 'Pekerjaan Mekanik', 'value' => $summary['mechanic_jobs'], 'color' => 'yellow'],
        ['label' => 'Pendapatan', 'value' => 'Rp ' . number_format($summary['payment_total'], 0, ',', '.'), 'color' => 'purple'],
        ['label' => 'Sparepart Terpakai', 'value' => $summary['sparepart_usage'], 'color' => 'orange'],
        ['label' => 'Aktivitas User', 'value' => $summary['audit_logs'], 'color' => 'blue'],
    ];
@endphp

<div class="space-y-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Laporan Operasional</h1>
            <p class="text-gray-400 mt-1">Ringkasan data pelanggan, order, mekanik, sparepart, pembayaran, dan aktivitas user.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('reports.export.pdf', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="w-fit px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                Export PDF
            </a>
            <a href="{{ route('reports.export.excel', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="w-fit px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                Export Excel
            </a>
            <a href="{{ route('audit-logs.index') }}" class="w-fit px-4 py-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-200 rounded-lg text-sm">
                Lihat Audit Log
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('reports.index') }}" class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-2xl p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-100">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-100">
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium">Terapkan</button>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-gray-300 rounded-lg text-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($summaryCards as $card)
        @php $colorClass = $cardColors[$card['color']] ?? $cardColors['blue']; @endphp
        <div class="bg-gray-800/60 border border-gray-700/50 rounded-2xl p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center border {{ $colorClass }} mb-4">
                <span class="w-2.5 h-2.5 rounded-full bg-current"></span>
            </div>
            <p class="text-2xl font-bold text-gray-100">{{ $card['value'] }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Laporan Order Servis</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                    <thead class="bg-gray-700/30 text-gray-400 uppercase">
                        <tr>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3">Kendaraan</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($orders as $order)
                        <tr>
                            <td class="px-5 py-4 text-orange-400 font-medium">{{ $order->order_number }}</td>
                            <td class="px-5 py-4">{{ $order->customer->name ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $order->vehicle->license_plate ?? '-' }}</td>
                            <td class="px-5 py-4"><x-ui.status-badge :status="$order->status" /></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada order pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Laporan Pembayaran</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                    <thead class="bg-gray-700/30 text-gray-400 uppercase">
                        <tr>
                            <th class="px-5 py-3">No Payment</th>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3">Kasir</th>
                            <th class="px-5 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($payments as $payment)
                        <tr>
                            <td class="px-5 py-4 text-orange-400 font-medium">{{ $payment->payment_number }}</td>
                            <td class="px-5 py-4">{{ $payment->serviceOrder->customer->name ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $payment->cashier->name ?? '-' }}</td>
                            <td class="px-5 py-4">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada pembayaran pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Laporan Pengerjaan Mekanik</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                    <thead class="bg-gray-700/30 text-gray-400 uppercase">
                        <tr>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Mekanik</th>
                            <th class="px-5 py-3">Mulai</th>
                            <th class="px-5 py-3">Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($mechanicJobs as $job)
                        <tr>
                            <td class="px-5 py-4 text-orange-400 font-medium">{{ $job->serviceOrder->order_number ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $job->mechanic->name ?? '-' }}</td>
                            <td class="px-5 py-4">{{ optional($job->started_at)->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-5 py-4">{{ optional($job->finished_at)->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada pekerjaan mekanik pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Penggunaan Sparepart</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                    <thead class="bg-gray-700/30 text-gray-400 uppercase">
                        <tr>
                            <th class="px-5 py-3">Kode</th>
                            <th class="px-5 py-3">Sparepart</th>
                            <th class="px-5 py-3">Qty</th>
                            <th class="px-5 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($usedSpareparts as $sparepart)
                        <tr>
                            <td class="px-5 py-4 text-orange-400 font-medium">{{ $sparepart->part_code }}</td>
                            <td class="px-5 py-4">{{ $sparepart->name }}</td>
                            <td class="px-5 py-4">{{ $sparepart->total_quantity }}</td>
                            <td class="px-5 py-4">Rp {{ number_format($sparepart->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada penggunaan sparepart pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Layanan Servis Terpakai</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                    <thead class="bg-gray-700/30 text-gray-400 uppercase">
                        <tr>
                            <th class="px-5 py-3">Kode</th>
                            <th class="px-5 py-3">Layanan</th>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($serviceUsage as $service)
                        <tr>
                            <td class="px-5 py-4 text-orange-400 font-medium">{{ $service->service_code }}</td>
                            <td class="px-5 py-4">{{ $service->name }}</td>
                            <td class="px-5 py-4">{{ $service->total_orders }}</td>
                            <td class="px-5 py-4">Rp {{ number_format($service->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada layanan servis pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-200">Aktivitas User Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                    <thead class="bg-gray-700/30 text-gray-400 uppercase">
                        <tr>
                            <th class="px-5 py-3">Waktu</th>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Modul</th>
                            <th class="px-5 py-3">Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($auditLogs as $log)
                        <tr>
                            <td class="px-5 py-4">{{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $log->user->name ?? 'System' }}</td>
                            <td class="px-5 py-4">{{ ucfirst(str_replace('_', ' ', $log->module)) }}</td>
                            <td class="px-5 py-4 text-gray-100">{{ $log->activity }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada aktivitas user pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
