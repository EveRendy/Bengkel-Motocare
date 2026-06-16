@extends('layouts.app')
@section('title', 'Manajemen Order Servis')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-100">Antrean & Order Servis</h1>
        <a href="{{ route('service-orders.create') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow whitespace-nowrap">
            + Buat Order Baru
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-gray-800 p-4 border border-gray-700 rounded-xl">
        <form method="GET" action="{{ route('service-orders.index') }}" class="flex items-end gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Filter Status</label>
                <select name="status" class="bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100" onchange="this.form.submit()">
                    <option value="semua">Semua Status</option>
                    @foreach(['terdaftar', 'menunggu', 'dikerjakan', 'selesai_servis', 'selesai', 'batal'] as $stat)
                        <option value="{{ $stat }}" {{ request('status') == $stat ? 'selected' : '' }}>
                            {{ strtoupper(str_replace('_', ' ', $stat)) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300 whitespace-nowrap">
                <thead class="bg-gray-700/50 text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3">Order #</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Kendaraan</th>
                        <th class="px-4 py-3">Mekanik</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 font-medium text-orange-400">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $order->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $order->vehicle->license_plate ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($order->mechanic_id)
                                {{ $order->mechanic->name }}
                            @else
                                <span class="text-gray-500 italic">Belum di-assign</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
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
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('service-orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 bg-blue-400/10 px-2 py-1 rounded">Detail</a>
                            
                            @if(in_array($order->status, ['terdaftar', 'menunggu']))
                                <a href="{{ route('service-orders.edit', $order) }}" class="text-yellow-400 hover:text-yellow-300 bg-yellow-400/10 px-2 py-1 rounded">Edit</a>
                                
                                @if(!$order->mechanic_id)
                                    <button onclick="openAssignModal('{{ route('service-orders.assign', $order) }}')" class="text-purple-400 hover:text-purple-300 bg-purple-400/10 px-2 py-1 rounded">Assign Mekanik</button>
                                @endif

                                <button onclick="openCancelModal('{{ route('service-orders.cancel', $order) }}')" class="text-red-400 hover:text-red-300 bg-red-400/10 px-2 py-1 rounded">Batal</button>
                            @endif

                            @if($order->status == 'menunggu' && $order->mechanic_id)
                                <form action="{{ route('service-orders.start', $order) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-orange-400 hover:text-orange-300 bg-orange-400/10 px-2 py-1 rounded" onclick="return confirm('Force start order ini?')">Kerjakan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada data order servis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-700">
            {{ $orders->links() }}
        </div>
    </div>
</div>

{{-- Modal Assign Mekanik --}}
<div id="assignModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/70 backdrop-blur-sm">
    <div class="bg-gray-800 border border-gray-700 rounded-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-100 mb-4">Assign Mekanik</h3>
        <form id="assignForm" method="POST" action="">
            @csrf @method('PATCH')
            <select name="mechanic_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 mb-4" required>
                <option value="">-- Pilih Mekanik --</option>
                @foreach($mechanics as $mekanik)
                    <option value="{{ $mekanik->id }}">{{ $mekanik->name }}</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-gray-400 hover:text-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">Assign</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pembatalan --}}
<div id="cancelModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/70 backdrop-blur-sm">
    <div class="bg-gray-800 border border-gray-700 rounded-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-red-400 mb-2">Batalkan Order</h3>
        <form id="cancelForm" method="POST" action="">
            @csrf @method('PATCH')
            <textarea name="cancel_reason" rows="3" placeholder="Alasan pembatalan (Wajib)..." required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 mb-4"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-gray-400 hover:text-gray-200">Kembali</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Konfirmasi Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAssignModal(url) {
        document.getElementById('assignForm').action = url;
        document.getElementById('assignModal').classList.remove('hidden');
    }
    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
    }
    function openCancelModal(url) {
        document.getElementById('cancelForm').action = url;
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }
</script>
@endsection