@extends('layouts.app')
@section('title', 'Detail Order Servis')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('service-orders.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
            <h1 class="text-2xl font-bold text-gray-100">Order: {{ $order->order_number }}</h1>
        </div>
        
        <div class="flex gap-2">
            @if(in_array($order->status, ['terdaftar', 'menunggu']))
                <a href="{{ route('service-orders.edit', $order) }}" class="px-4 py-2 bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 border border-yellow-500/20 rounded-lg shadow font-medium">Edit / Assign</a>
                <form action="{{ route('service-orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Batalkan order ini?')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="cancel_reason" value="Dibatalkan dari halaman detail oleh Admin/SA">
                    <button type="submit" class="px-4 py-2 bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 rounded-lg shadow font-medium">Batalkan</button>
                </form>
            @endif
            
            @if($order->status == 'menunggu' && $order->mechanic_id)
                {{-- Tombol Force Start (Hanya untuk emergency) --}}
                <form action="{{ route('service-orders.start', $order) }}" method="POST" onsubmit="return confirm('Mekanik belum mengklik mulai. Yakin ingin memaksa mulai status order ini? (Normalnya mekanik yang menekan tombol mulai di akun mereka)')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-orange-600/50 hover:bg-orange-700 text-white border border-orange-500/50 rounded-lg shadow text-sm font-medium">⚠️ Force Start</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success')) <div class="p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl">{{ session('error') }}</div> @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Status & Penugasan</h2>
                <div class="space-y-4">
                    <div>
                        <span class="block text-gray-500 text-sm mb-1">Status Order</span>
                        @php
                            $statusColors = [
                                'terdaftar' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                'menunggu' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                'dikerjakan' => 'bg-orange-500/10 text-orange-400 border border-orange-500/20',
                                'selesai_servis' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                                'selesai' => 'bg-green-500/10 text-green-400 border border-green-500/20',
                                'batal' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                            ];
                            $color = $statusColors[$order->status] ?? '';
                        @endphp
                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ $color }}">
                            {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-sm mb-1">Service Advisor</span>
                        <div class="text-gray-200">{{ $order->serviceAdvisor->name ?? 'Belum Ditugaskan' }}</div>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-sm mb-1">Mekanik</span>
                        <div class="text-gray-200">{{ $order->mechanic->name ?? 'Belum Ditugaskan' }}</div>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-sm mb-1">Tanggal Dibuat</span>
                        <div class="text-gray-200">{{ $order->created_at ? $order->created_at->format('d M Y H:i') : \Carbon\Carbon::parse($order->order_date)->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Informasi Kendaraan</h2>
                <div class="space-y-3 text-sm">
                    <div><span class="block text-gray-500">Plat Nomor</span><span class="text-gray-200 font-bold text-lg">{{ $order->vehicle->license_plate }}</span></div>
                    <div><span class="block text-gray-500">Merk & Tipe</span><span class="text-gray-200">{{ $order->vehicle->brand }} {{ $order->vehicle->type }}</span></div>
                    <div><span class="block text-gray-500">Pelanggan</span><span class="text-gray-200">{{ $order->customer->name }}</span></div>
                    <div><span class="block text-gray-500">Kilometer Saat Ini</span><span class="text-gray-200">{{ number_format($order->current_km, 0, ',', '.') }} KM</span></div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Keluhan & Catatan</h2>
                <div class="space-y-4">
                    <div>
                        <span class="block text-gray-500 text-sm mb-1">Keluhan Pelanggan:</span>
                        <p class="text-gray-200 bg-gray-900/50 p-3 rounded-lg border border-gray-700">{{ $order->complaint }}</p>
                    </div>
                    @if($order->notes)
                    <div>
                        <span class="block text-gray-500 text-sm mb-1">Catatan Internal:</span>
                        <p class="text-gray-400 bg-gray-900/30 p-3 rounded-lg border border-gray-700">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-2">
                    <h2 class="text-lg font-semibold text-white">Layanan yang Dipesan</h2>
                    <span class="text-gray-400 text-sm">Estimasi Biaya: <strong class="text-green-400">Rp {{ number_format($order->estimated_cost, 0, ',', '.') }}</strong></span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead class="bg-gray-700/50 text-gray-400">
                            <tr>
                                <th class="px-4 py-2">Nama Layanan</th>
                                <th class="px-4 py-2 text-right">Harga Saat Pesan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @forelse($order->services as $service)
                            <tr>
                                <td class="px-4 py-3">{{ $service->name }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($service->pivot->price_at_time, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-gray-500">Tidak ada layanan yang dipilih.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
