@extends('layouts.app')
@section('title', 'Riwayat Servis Saya')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-100">Riwayat Servis Kendaraan Saya</h1>
        <a href="{{ route('booking.create') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow whitespace-nowrap">
            + Booking Baru
        </a>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">No. Order</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kendaraan</th>
                        <th class="px-4 py-3">Keluhan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 font-medium text-orange-400">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $order->vehicle->license_plate ?? '-' }}</td>
                        <td class="px-4 py-3 truncate max-w-[200px]" title="{{ $order->complaint }}">{{ \Illuminate\Support\Str::limit($order->complaint, 30) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'terdaftar' => 'bg-blue-500/10 text-blue-400',
                                    'menunggu' => 'bg-yellow-500/10 text-yellow-400',
                                    'dikerjakan' => 'bg-orange-500/10 text-orange-400',
                                    'selesai_servis' => 'bg-purple-500/10 text-purple-400',
                                    'selesai' => 'bg-green-500/10 text-green-400',
                                    'batal' => 'bg-red-500/10 text-red-400',
                                ];
                                $color = $statusColors[$order->status] ?? 'bg-gray-500/10 text-gray-400';
                                $statusText = str_replace('_', ' ', strtoupper($order->status));
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right flex justify-end gap-2">
                            <a href="{{ route('booking.show', $order) }}" class="text-blue-400 hover:text-blue-300">Detail</a>
                            @if(in_array($order->status, ['terdaftar', 'menunggu']))
                            <button onclick="openCancelModal('{{ route('booking.cancel', $order) }}')" class="text-red-400 hover:text-red-300 ml-2">Batalkan</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Anda belum pernah melakukan booking servis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700">
            {{ $orders->links() }}
        </div>
    </div>
</div>

{{-- Modal Pembatalan Sederhana Menggunakan Alpine.js atau Vanilla JS --}}
<div id="cancelModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/70 backdrop-blur-sm">
    <div class="bg-gray-800 border border-gray-700 rounded-xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-gray-100 mb-2">Batalkan Booking</h3>
        <p class="text-gray-400 text-sm mb-4">Apakah Anda yakin ingin membatalkan order ini? Aksi ini tidak dapat dibatalkan.</p>
        <form id="cancelForm" method="POST" action="">
            @csrf @method('PATCH')
            <textarea name="cancel_reason" rows="3" placeholder="Tulis alasan pembatalan..." required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-red-500/50 mb-4"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-gray-400 hover:text-gray-200">Kembali</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal(url) {
        document.getElementById('cancelForm').action = url;
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }
</script>
@endsection