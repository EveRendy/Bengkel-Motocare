@extends('layouts.app')
@section('title', 'Detail Pekerjaan Mekanik')

@section('content')
@php
    $canHandle = auth()->user()->hasRole('mekanik') && $order->mechanic_id === auth()->id();
    $canEditJob = $canHandle && $order->status === 'dikerjakan';
    $savedRows = $order->spareparts->map(fn ($sparepart) => [
        'id' => $sparepart->id,
        'quantity' => $sparepart->pivot->quantity,
    ])->values()->all();
    $sparepartRows = old('spareparts', $savedRows);
    $rowCount = max(5, count($sparepartRows));
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('mechanic-jobs.index') }}" class="text-gray-400 hover:text-gray-200">&larr; Kembali</a>
            <div>
                <h1 class="text-2xl font-bold text-gray-100">Pekerjaan: {{ $order->order_number }}</h1>
                <div class="mt-2">
                    <x-ui.status-badge :status="$order->status" />
                </div>
            </div>
        </div>

        @if($canHandle && $order->status === 'menunggu')
            <form method="POST" action="{{ route('mechanic-jobs.start', $order) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow whitespace-nowrap">
                    Mulai Kerjakan
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Informasi Order">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 block">Tanggal Servis</span>
                        <span class="text-gray-200">{{ $order->order_date->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Kilometer Masuk</span>
                        <span class="text-gray-200">{{ number_format($order->current_km, 0, ',', '.') }} KM</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Pelanggan</span>
                        <span class="text-gray-200">{{ $order->customer->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Kendaraan</span>
                        <span class="text-gray-200">{{ $order->vehicle->license_plate ?? '-' }} - {{ $order->vehicle->brand ?? '-' }} {{ $order->vehicle->type ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Service Advisor</span>
                        <span class="text-gray-200">{{ $order->serviceAdvisor->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Mekanik</span>
                        <span class="text-gray-200">{{ $order->mechanic->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-700">
                    <span class="text-gray-500 text-sm block mb-1">Keluhan Pelanggan</span>
                    <p class="text-gray-300 text-sm bg-gray-900/50 p-3 rounded-lg">{{ $order->complaint }}</p>
                </div>

                @if($order->notes)
                    <div class="mt-4">
                        <span class="text-gray-500 text-sm block mb-1">Catatan Internal</span>
                        <p class="text-gray-300 text-sm bg-gray-900/50 p-3 rounded-lg whitespace-pre-line">{{ $order->notes }}</p>
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card title="Catatan Pengerjaan">
                @if($order->status === 'menunggu')
                    <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg text-yellow-400 text-sm">
                        Pekerjaan belum dimulai. Klik tombol Mulai Kerjakan untuk mengisi hasil pemeriksaan.
                    </div>
                @else
                    <form method="POST" action="{{ route('mechanic-jobs.update', $order) }}" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Hasil Pemeriksaan</label>
                            <textarea name="inspection_result" rows="4" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" {{ $canEditJob ? '' : 'disabled' }} required>{{ old('inspection_result', $order->mechanicJob->inspection_result ?? '') }}</textarea>
                            <x-ui.field-error name="inspection_result" />
                        </div>

                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Tindakan Servis</label>
                            <textarea name="service_actions" rows="4" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" {{ $canEditJob ? '' : 'disabled' }} required>{{ old('service_actions', $order->mechanicJob->service_actions ?? '') }}</textarea>
                            <x-ui.field-error name="service_actions" />
                        </div>

                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Catatan Mekanik</label>
                            <textarea name="mechanic_notes" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" {{ $canEditJob ? '' : 'disabled' }}>{{ old('mechanic_notes', $order->mechanicJob->mechanic_notes ?? '') }}</textarea>
                            <x-ui.field-error name="mechanic_notes" />
                        </div>

                        <div class="border-t border-gray-700 pt-5">
                            <h3 class="text-sm font-semibold text-gray-200 mb-3">Sparepart Digunakan</h3>
                            <div class="space-y-3">
                                @for($i = 0; $i < $rowCount; $i++)
                                    @php $row = $sparepartRows[$i] ?? ['id' => null, 'quantity' => null]; @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                        <div class="md:col-span-3">
                                            <select name="spareparts[{{ $i }}][id]" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" {{ $canEditJob ? '' : 'disabled' }}>
                                                <option value="">-- Pilih Sparepart --</option>
                                                @foreach($spareparts as $sparepart)
                                                    <option value="{{ $sparepart->id }}" {{ (string) ($row['id'] ?? '') === (string) $sparepart->id ? 'selected' : '' }}>
                                                        {{ $sparepart->name }} - Rp {{ number_format($sparepart->price, 0, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-ui.field-error :name="'spareparts.' . $i . '.id'" />
                                        </div>
                                        <div>
                                            <input type="number" min="1" name="spareparts[{{ $i }}][quantity]" value="{{ $row['quantity'] ?? '' }}" placeholder="Qty" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" {{ $canEditJob ? '' : 'disabled' }}>
                                            <x-ui.field-error :name="'spareparts.' . $i . '.quantity'" />
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        @if($canEditJob)
                            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                                <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">
                                    Simpan Catatan
                                </button>
                                <button type="submit" formaction="{{ route('mechanic-jobs.finish', $order) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                                    Tandai Selesai Servis
                                </button>
                            </div>
                        @endif
                    </form>
                @endif
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Layanan Servis">
                <div class="space-y-3">
                    @forelse($order->services as $service)
                        <div class="flex justify-between gap-3 text-sm border-b border-gray-700/50 pb-3 last:border-0 last:pb-0">
                            <span class="text-gray-300">{{ $service->name }}</span>
                            <span class="text-orange-400 font-semibold whitespace-nowrap">Rp {{ number_format($service->pivot->price_at_time, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada layanan dipilih.</p>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card title="Waktu Pengerjaan">
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="text-gray-500 block">Mulai</span>
                        <span class="text-gray-200">{{ optional($order->mechanicJob?->started_at)->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Selesai</span>
                        <span class="text-gray-200">{{ optional($order->mechanicJob?->finished_at)->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
