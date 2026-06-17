@extends('layouts.app')
@section('title', 'Daftar Booking Pelanggan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-100">Daftar Booking Masuk</h1>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-900/20 border border-blue-500/30 p-4 rounded-xl flex gap-3 items-start">
        <span class="text-blue-400 mt-0.5">ℹ️</span>
        <p class="text-blue-200 text-sm">Daftar ini menampilkan booking yang dilakukan oleh pelanggan dan belum diproses menjadi Order Servis. Silakan proses booking untuk menentukan layanan dan estimasi biaya.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl">{{ session('error') }}</div>
    @endif

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                <thead class="bg-gray-700/50 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">No Booking</th>
                        <th class="px-4 py-3">Tanggal Diajukan</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Kendaraan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse ($bookings as $order)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 font-medium text-orange-400">{{ $order->order_number ?? '-' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $order->customer->name }}</td>
                        <td class="px-4 py-3">{{ $order->vehicle->license_plate }} ({{ $order->vehicle->brand }})</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                TERDAFTAR
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('bookings.process.form', $order) }}" class="text-white hover:text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg shadow font-medium transition inline-block">Proses Order</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada booking dari pelanggan saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection