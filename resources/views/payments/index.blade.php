@extends('layouts.app')
@section('title', 'Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Pembayaran</h1>
            <p class="text-gray-400 mt-1 text-sm">Kelola pembayaran order yang sudah selesai servis.</p>
        </div>
    </div>

    <div class="bg-gray-800 p-4 border border-gray-700 rounded-xl">
        <form method="GET" action="{{ route('payments.index') }}" class="flex items-end gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Filter Pembayaran</label>
                <select name="status" class="bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" onchange="this.form.submit()">
                    <option value="semua">Semua</option>
                    <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
        </form>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                <thead class="bg-gray-700/50 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">Order #</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Kendaraan</th>
                        <th class="px-4 py-3">Mekanik</th>
                        <th class="px-4 py-3">Status Order</th>
                        <th class="px-4 py-3">Status Bayar</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 font-medium text-orange-400">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $order->vehicle->license_plate ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $order->mechanic->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <x-ui.status-badge :status="$order->status" />
                        </td>
                        <td class="px-4 py-3">
                            @if($order->payment && $order->payment->status === 'lunas')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-500/10 text-green-400 border border-green-500/20">LUNAS</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">BELUM BAYAR</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('payments.show', $order) }}" class="text-blue-400 hover:text-blue-300 bg-blue-400/10 px-2 py-1 rounded">
                                Tagihan
                            </a>
                            @if($order->payment)
                                <a href="{{ route('invoices.show', $order->payment) }}" class="text-green-400 hover:text-green-300 bg-green-400/10 px-2 py-1 rounded">
                                    Invoice
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada order yang siap dibayar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
