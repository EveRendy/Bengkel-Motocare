@extends('layouts.app')
@section('title', 'Invoice')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print:hidden">
        <div class="flex items-center gap-4">
            <a href="{{ route('payments.show', $order) }}" class="text-gray-400 hover:text-gray-200">&larr; Kembali</a>
            <h1 class="text-2xl font-bold text-gray-100">Invoice {{ $payment->invoice->invoice_number ?? '-' }}</h1>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow">
            Cetak Invoice
        </button>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm print:bg-white print:text-gray-900 print:border-0 print:shadow-none">
        <div class="p-8 border-b border-gray-700 print:border-gray-300">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20 print:shadow-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-100 print:text-gray-900">MotoCare</h2>
                        <p class="text-sm text-gray-400 print:text-gray-600">Sistem Informasi Bengkel Motor</p>
                    </div>
                </div>

                <div class="text-left sm:text-right text-sm">
                    <p class="text-gray-500 print:text-gray-600">Invoice</p>
                    <p class="text-xl font-bold text-orange-400 print:text-gray-900">{{ $payment->invoice->invoice_number ?? '-' }}</p>
                    <p class="text-gray-400 print:text-gray-600 mt-1">{{ $payment->payment_date?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-gray-700 print:border-gray-300">
            <div class="space-y-2 text-sm">
                <h3 class="text-gray-100 font-semibold mb-3 print:text-gray-900">Ditagihkan Kepada</h3>
                <p class="text-gray-300 print:text-gray-800">{{ $order->customer->name ?? '-' }}</p>
                <p class="text-gray-400 print:text-gray-600">{{ $order->customer->phone ?? '-' }}</p>
                <p class="text-gray-400 print:text-gray-600">{{ $order->customer->address ?? '-' }}</p>
            </div>

            <div class="space-y-2 text-sm md:text-right">
                <h3 class="text-gray-100 font-semibold mb-3 print:text-gray-900">Detail Order</h3>
                <p class="text-gray-300 print:text-gray-800">{{ $order->order_number }}</p>
                <p class="text-gray-400 print:text-gray-600">{{ $order->vehicle->license_plate ?? '-' }} - {{ $order->vehicle->brand ?? '-' }} {{ $order->vehicle->type ?? '' }}</p>
                <p class="text-gray-400 print:text-gray-600">Kasir: {{ $payment->cashier->name ?? '-' }}</p>
            </div>
        </div>

        <div class="p-8 space-y-8">
            <div>
                <h3 class="text-gray-100 font-semibold mb-3 print:text-gray-900">Rincian Layanan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300 print:text-gray-800">
                        <thead class="text-gray-400 border-b border-gray-700 print:text-gray-600 print:border-gray-300">
                            <tr>
                                <th class="py-2">Layanan</th>
                                <th class="py-2 text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50 print:divide-gray-200">
                            @foreach($order->services as $service)
                            <tr>
                                <td class="py-3">{{ $service->name }}</td>
                                <td class="py-3 text-right">Rp {{ number_format($service->pivot->price_at_time, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h3 class="text-gray-100 font-semibold mb-3 print:text-gray-900">Rincian Sparepart</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300 print:text-gray-800">
                        <thead class="text-gray-400 border-b border-gray-700 print:text-gray-600 print:border-gray-300">
                            <tr>
                                <th class="py-2">Sparepart</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Harga</th>
                                <th class="py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50 print:divide-gray-200">
                            @forelse($order->spareparts as $sparepart)
                            <tr>
                                <td class="py-3">{{ $sparepart->name }}</td>
                                <td class="py-3 text-center">{{ $sparepart->pivot->quantity }}</td>
                                <td class="py-3 text-right">Rp {{ number_format($sparepart->pivot->price_at_time, 0, ',', '.') }}</td>
                                <td class="py-3 text-right">Rp {{ number_format($sparepart->pivot->price_at_time * $sparepart->pivot->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-3 text-center text-gray-500">Tidak ada sparepart.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end">
                <div class="w-full max-w-sm space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 print:text-gray-600">Total Jasa</span>
                        <span class="text-gray-200 print:text-gray-800">Rp {{ number_format($payment->total_service, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 print:text-gray-600">Total Sparepart</span>
                        <span class="text-gray-200 print:text-gray-800">Rp {{ number_format($payment->total_sparepart, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 print:text-gray-600">Diskon</span>
                        <span class="text-gray-200 print:text-gray-800">Rp {{ number_format($payment->discount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-700 pt-3 print:border-gray-300">
                        <span class="text-gray-100 font-semibold print:text-gray-900">Total Bayar</span>
                        <span class="text-2xl text-green-400 font-bold print:text-gray-900">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 print:text-gray-600">Metode</span>
                        <span class="text-gray-200 print:text-gray-800">{{ strtoupper($payment->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 print:text-gray-600">Status</span>
                        <span class="text-green-400 font-semibold print:text-gray-900">{{ strtoupper($payment->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
