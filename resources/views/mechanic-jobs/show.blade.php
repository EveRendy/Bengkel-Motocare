@extends('layouts.app')
@section('title', 'Detail Pekerjaan Mekanik')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('mechanic-jobs.index') }}" class="text-gray-400 hover:text-gray-200">← Kembali</a>
            <h1 class="text-2xl font-bold text-gray-100">Pekerjaan: {{ $job->serviceOrder->order_number }}</h1>
        </div>
        
        <div class="flex gap-2">
            @if(auth()->user()->hasRole('mekanik') && auth()->id() === $job->mechanic_id)
                
                {{-- Tombol Mulai --}}
                @if($job->serviceOrder->status === 'menunggu')
                    <form action="{{ route('mechanic-jobs.start', $job) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow font-medium">🚀 Mulai Kerjakan</button>
                    </form>
                @endif

                {{-- Tombol Hasil & Selesai --}}
                @if($job->serviceOrder->status === 'dikerjakan')
                    <a href="{{ route('mechanic-jobs.edit', $job) }}" class="px-5 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg shadow font-medium">📝 Input Hasil</a>
                    
                    <form action="{{ route('mechanic-jobs.complete', $job) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="button" data-confirm-submit data-confirm-variant="warning" data-confirm-title="Selesaikan Pekerjaan" data-confirm-message="Tandai pekerjaan ini selesai? Pastikan hasil pemeriksaan dan tindakan servis sudah diisi." data-confirm-text="Tandai Selesai" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow font-medium">✅ Tandai Selesai Servis</button>
                    </form>
                @endif
                
            @endif
        </div>
    </div>

    @if(session('success')) <div class="p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl">{{ session('error') }}</div> @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI: INFO ORDER & KENDARAAN --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Informasi Kendaraan</h2>
                <div class="space-y-3 text-sm">
                    @if($job->serviceOrder->vehicle_id)
                        <div><span class="block text-gray-500">Plat Nomor</span><span class="text-gray-200 font-bold text-xl">{{ $job->serviceOrder->vehicle->license_plate }}</span></div>
                        <div><span class="block text-gray-500">Merk & Tipe</span><span class="text-gray-200">{{ $job->serviceOrder->vehicle->brand }} {{ $job->serviceOrder->vehicle->type }} ({{ $job->serviceOrder->vehicle->year }})</span></div>
                    @else
                        <div><span class="block text-gray-500">Plat Nomor</span><span class="text-gray-200 font-bold text-xl">{{ $job->serviceOrder->walkin_vehicle_license_plate ?? '-' }}</span></div>
                        <div><span class="block text-gray-500">Merk & Tipe</span><span class="text-gray-200">{{ $job->serviceOrder->walkin_vehicle_brand }} {{ $job->serviceOrder->walkin_vehicle_type }} ({{ $job->serviceOrder->walkin_vehicle_year }})</span></div>
                    @endif
                    <div><span class="block text-gray-500">Kilometer Masuk</span><span class="text-gray-200">{{ number_format($job->serviceOrder->current_km, 0, ',', '.') }} KM</span></div>
                    <div class="pt-3 border-t border-gray-700">
                        <span class="block text-gray-500 mb-1">Keluhan Pelanggan:</span>
                        <p class="text-gray-300 italic bg-gray-900/50 p-3 rounded border border-gray-700">"{{ $job->serviceOrder->complaint }}"</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Layanan Dipesan</h2>
                <ul class="space-y-2 text-sm text-gray-300">
                    @foreach($job->serviceOrder->services as $service)
                        <li class="flex justify-between items-center py-1 border-b border-gray-700/50 last:border-0">
                            <span>- {{ $service->name }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- KOLOM KANAN: HASIL PENGERJAAN & SPAREPART --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Hasil Pekerjaan --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-2">
                    <h2 class="text-lg font-semibold text-white">Catatan Mekanik</h2>
                    @if(auth()->user()->hasRole('mekanik') && $job->serviceOrder->status == 'dikerjakan')
                        <a href="{{ route('mechanic-jobs.edit', $job) }}" class="text-yellow-400 hover:text-yellow-300 text-sm bg-yellow-400/10 px-3 py-1 rounded border border-yellow-400/20">Input / Edit Hasil</a>
                    @endif
                </div>
                
                @if($job->inspection_result || $job->service_actions)
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-gray-500 mb-1">Hasil Pemeriksaan:</span>
                            <p class="text-gray-200 bg-gray-900/50 p-3 rounded border border-gray-700">{{ $job->inspection_result }}</p>
                        </div>
                        <div>
                            <span class="block text-gray-500 mb-1">Tindakan Servis:</span>
                            <p class="text-gray-200 bg-gray-900/50 p-3 rounded border border-gray-700">{{ $job->service_actions }}</p>
                        </div>
                        @if($job->mechanic_notes)
                        <div>
                            <span class="block text-gray-500 mb-1">Catatan Tambahan:</span>
                            <p class="text-gray-400 bg-gray-900/30 p-3 rounded border border-gray-700">{{ $job->mechanic_notes }}</p>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="p-6 text-center border border-dashed border-gray-600 rounded bg-gray-900/30">
                        <p class="text-gray-500">Belum ada catatan pemeriksaan.</p>
                        @if(auth()->user()->hasRole('mekanik') && $job->serviceOrder->status == 'dikerjakan')
                            <a href="{{ route('mechanic-jobs.edit', $job) }}" class="mt-2 inline-block text-orange-400 hover:text-orange-300 underline">Klik di sini untuk mengisi</a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Penggunaan Sparepart --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">Sparepart Digunakan</h2>
                
                <div class="overflow-x-auto mb-4 border border-gray-700 rounded-lg">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead class="bg-gray-700/50 text-gray-400 border-b border-gray-700">
                            <tr>
                                <th class="px-4 py-3">Nama Part</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                @if(auth()->user()->hasRole('mekanik') && $job->serviceOrder->status == 'dikerjakan')
                                <th class="px-4 py-3 text-right">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50 bg-gray-900/20">
                            @forelse($job->serviceOrder->spareparts as $part)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="block">{{ $part->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $part->part_code }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold">{{ $part->pivot->quantity }}</td>
                                @if(auth()->user()->hasRole('mekanik') && $job->serviceOrder->status == 'dikerjakan')
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ route('mechanic-jobs.spareparts.remove', [$job, $part->id]) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="pivot_id" value="{{ $part->pivot->id }}">
                                        <button type="button" data-confirm-submit data-confirm-title="Hapus Sparepart" data-confirm-message="Hapus sparepart ini dari pengerjaan?" data-confirm-text="Hapus" class="text-red-400 hover:text-red-300 bg-red-400/10 px-2 py-1 rounded border border-red-400/20">Hapus</button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada sparepart yang digunakan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(auth()->user()->hasRole('mekanik') && $job->serviceOrder->status == 'dikerjakan')
                <div class="bg-gray-900/50 border border-gray-700 p-4 rounded-lg">
                    <form action="{{ route('mechanic-jobs.spareparts.store', $job) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <div class="flex-1">
                            <select name="sparepart_id" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-gray-100 focus:ring-2 focus:ring-orange-500" required>
                                <option value="">-- Tambah Sparepart Baru --</option>
                                @foreach($spareparts as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }} (Rp {{ number_format($sp->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-24">
                            <input type="number" name="quantity" value="1" min="1" placeholder="Qty" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-gray-100 focus:ring-2 focus:ring-orange-500" required>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow font-medium whitespace-nowrap">Tambah Part</button>
                    </form>
                    @error('sparepart_id') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
