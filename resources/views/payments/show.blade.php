@extends('layouts.app')
@section('title', 'Detail Tagihan')

@section('content')
@php
    $discount = old('discount', $order->payment->discount ?? 0);
    $payableTotal = max(0, $totals['grand_total'] - (float) $discount);
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-200">&larr; Kembali</a>
            <div>
                <h1 class="text-2xl font-bold text-gray-100">Tagihan: {{ $order->order_number }}</h1>
                <div class="mt-2">
                    <x-ui.status-badge :status="$order->status" />
                </div>
            </div>
        </div>

        @if($order->payment)
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('invoices.show', $order->payment) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow whitespace-nowrap">
                    Lihat Invoice
                </a>
                <a href="{{ route('invoices.pdf', $order->payment) }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow whitespace-nowrap">
                    Download PDF
                </a>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Informasi Order">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 block">Pelanggan</span>
                        <span class="text-gray-200">{{ $order->customer->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Kendaraan</span>
                        <span class="text-gray-200">{{ $order->vehicle->license_plate ?? '-' }} - {{ $order->vehicle->brand ?? '-' }} {{ $order->vehicle->type ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Mekanik</span>
                        <span class="text-gray-200">{{ $order->mechanic->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Tanggal Servis</span>
                        <span class="text-gray-200">{{ $order->order_date->format('d M Y') }}</span>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Rincian Layanan">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead class="text-gray-400 border-b border-gray-700">
                            <tr>
                                <th class="py-2">Layanan</th>
                                <th class="py-2 text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @forelse($order->services as $service)
                            <tr>
                                <td class="py-3">{{ $service->name }}</td>
                                <td class="py-3 text-right text-orange-400 font-semibold">Rp {{ number_format($service->pivot->price_at_time, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="py-4 text-center text-gray-500">Tidak ada layanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <x-ui.card title="Rincian Sparepart">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead class="text-gray-400 border-b border-gray-700">
                            <tr>
                                <th class="py-2">Sparepart</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Harga</th>
                                <th class="py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @forelse($order->spareparts as $sparepart)
                            <tr>
                                <td class="py-3">{{ $sparepart->name }}</td>
                                <td class="py-3 text-center">{{ $sparepart->pivot->quantity }}</td>
                                <td class="py-3 text-right">Rp {{ number_format($sparepart->pivot->price_at_time, 0, ',', '.') }}</td>
                                <td class="py-3 text-right text-orange-400 font-semibold">Rp {{ number_format($sparepart->pivot->price_at_time * $sparepart->pivot->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">Tidak ada sparepart.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Ringkasan Tagihan">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Jasa</span>
                        <span class="text-gray-200">Rp {{ number_format($totals['total_service'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Sparepart</span>
                        <span class="text-gray-200">Rp {{ number_format($totals['total_sparepart'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-700 pt-3">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-gray-200">Rp {{ number_format($totals['grand_total'], 0, ',', '.') }}</span>
                    </div>

                    @if($order->payment)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Diskon</span>
                            <span class="text-gray-200">Rp {{ number_format($order->payment->discount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-700 pt-3">
                            <span class="text-gray-200 font-semibold">Total Bayar</span>
                            <span class="text-2xl text-green-400 font-bold">Rp {{ number_format($order->payment->total_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </x-ui.card>

            @if(!$order->payment && auth()->user()->can('process-payment'))
                <x-ui.card title="Input Pembayaran">
                    <form method="POST" action="{{ route('payments.store', $order) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Metode Pembayaran</label>
                            <select name="payment_method" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" required>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS Manual</option>
                            </select>
                            <x-ui.field-error name="payment_method" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Diskon</label>
                            <input type="number" min="0" name="discount" value="{{ old('discount', 0) }}" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100">
                            <x-ui.field-error name="discount" />
                        </div>
                        <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Setelah Diskon</span>
                                <span class="text-xl text-green-400 font-bold">Rp {{ number_format($payableTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg" onclick="return confirm('Proses pembayaran dan generate invoice?')">
                            Proses Pembayaran
                        </button>
                    </form>
                </x-ui.card>
            @elseif($order->payment)
                <x-ui.card title="Data Pembayaran">
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500 block">No Pembayaran</span>
                            <span class="text-gray-200">{{ $order->payment->payment_number }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Metode</span>
                            <span class="text-gray-200">{{ strtoupper($order->payment->payment_method) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Kasir</span>
                            <span class="text-gray-200">{{ $order->payment->cashier->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Tanggal Bayar</span>
                            <span class="text-gray-200">{{ $order->payment->payment_date?->format('d M Y H:i') ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Status</span>
                            <span class="text-green-400 font-semibold">{{ strtoupper($order->payment->status) }}</span>
                        </div>
                    </div>
                </x-ui.card>

                @can('cancel-payment')
                    @if($order->payment->status === 'lunas')
                    <x-ui.card title="Batalkan Pembayaran">
                        <form method="POST" action="{{ route('payments.cancel', $order->payment) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <p class="text-sm text-gray-400">
                                Pembatalan tidak menghapus data payment/invoice. Status payment akan menjadi batal dan order kembali siap dibayar.
                            </p>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Alasan Pembatalan</label>
                                <textarea name="cancel_reason" rows="4" required minlength="10" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" placeholder="Contoh: Koreksi nominal karena diskon salah input."></textarea>
                                <x-ui.field-error name="cancel_reason" />
                            </div>
                            <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg" onclick="return confirm('Batalkan pembayaran ini? Data payment dan invoice tetap disimpan sebagai riwayat.')">
                                Batalkan Pembayaran
                            </button>
                        </form>
                    </x-ui.card>
                    @endif
                @endcan
            @else
                <x-ui.card title="Status Pembayaran">
                    <p class="text-sm text-gray-400">Order ini siap dibayar. Proses input pembayaran hanya tersedia untuk role kasir.</p>
                </x-ui.card>
            @endif

            @if($order->payments->where('status', 'batal')->count() > 0)
            <x-ui.card title="Riwayat Pembayaran Batal">
                <div class="space-y-3">
                    @foreach($order->payments->where('status', 'batal') as $payment)
                    <div class="bg-gray-900/50 border border-red-500/20 rounded-lg p-3 text-sm">
                        <div class="flex justify-between gap-3">
                            <span class="text-red-400 font-semibold">{{ $payment->payment_number }}</span>
                            <span class="text-gray-500">{{ $payment->updated_at->format('d M Y H:i') }}</span>
                        </div>
                        <p class="text-gray-400 mt-2">{{ $payment->cancel_reason ?? '-' }}</p>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>
            @endif
        </div>
    </div>
</div>
@endsection
