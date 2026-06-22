@extends('layouts.app')
@section('title', 'Detail Order Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('booking.index') }}" class="text-gray-400 hover:text-gray-200">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-100">Detail Order: {{ $order->order_number }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Informasi Booking</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Servis:</span>
                    <span class="text-gray-200">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status:</span>
                    <span class="font-bold text-orange-400">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kendaraan:</span>
                    <span class="text-gray-200">{{ $order->vehicle->license_plate }} ({{ $order->vehicle->brand }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kilometer:</span>
                    <span class="text-gray-200">{{ number_format($order->current_km, 0, ',', '.') }} KM</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-700">
                <span class="text-gray-500 text-sm block mb-1">Keluhan:</span>
                <p class="text-gray-300 text-sm bg-gray-900/50 p-3 rounded-lg">{{ $order->complaint }}</p>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Progres Servis</h2>
            <div class="space-y-4">
                <div>
                    <span class="text-gray-500 text-sm block">Service Advisor:</span>
                    <span class="text-gray-200">{{ $order->serviceAdvisor->name ?? 'Belum Ditugaskan' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">Mekanik yang Menangani:</span>
                    <span class="text-gray-200">{{ $order->mechanic->name ?? 'Belum Ditugaskan' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">Estimasi Biaya Jasa:</span>
                    <span class="text-xl font-bold text-green-400">Rp {{ number_format($order->estimated_cost, 0, ',', '.') }}</span>
                    <p class="text-xs text-gray-500 mt-1">*Hanya estimasi biaya layanan, belum termasuk sparepart</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-white">Informasi Pembayaran</h2>
                @if($order->payment)
                    <p class="text-sm text-gray-400 mt-1">
                        Invoice {{ $order->payment->invoice->invoice_number ?? '-' }} - {{ strtoupper($order->payment->status) }}
                    </p>
                    <p class="text-2xl font-bold text-green-400 mt-2">
                        Rp {{ number_format($order->payment->total_amount, 0, ',', '.') }}
                    </p>
                @else
                    <p class="text-sm text-gray-400 mt-1">Pembayaran belum diproses oleh kasir.</p>
                @endif
            </div>

            @if($order->payment)
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('invoices.show', $order->payment) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow text-sm">
                    Lihat Invoice
                </a>
                <a href="{{ route('invoices.pdf', $order->payment) }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow text-sm">
                    Download PDF
                </a>
            </div>
            @endif
        </div>
    </div>

    @if($order->mechanicJob)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Laporan Hasil Mekanik</h2>
            
            @if($order->mechanicJob->inspection_result)
                <div class="mb-4">
                    <span class="text-gray-500 text-sm block mb-1">Hasil Pemeriksaan:</span>
                    <p class="text-gray-200 bg-gray-900/50 p-3 rounded-lg">{{ $order->mechanicJob->inspection_result }}</p>
                </div>
            @endif

            @if($order->mechanicJob->service_actions)
                <div class="mb-4">
                    <span class="text-gray-500 text-sm block mb-1">Tindakan Servis:</span>
                    <p class="text-gray-200 bg-gray-900/50 p-3 rounded-lg">{{ $order->mechanicJob->service_actions }}</p>
                </div>
            @endif

            @if($order->mechanicJob->mechanic_notes)
                <div>
                    <span class="text-gray-500 text-sm block mb-1">Catatan Tambahan:</span>
                    <p class="text-gray-300 bg-gray-900/50 p-3 rounded-lg">{{ $order->mechanicJob->mechanic_notes }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
