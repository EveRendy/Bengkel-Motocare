@props(['status'])

@php
    $statusColors = [
        'terdaftar' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
        'menunggu' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
        'dikerjakan' => 'bg-orange-500/10 text-orange-400 border border-orange-500/20',
        'selesai_servis' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
        'selesai' => 'bg-green-500/10 text-green-400 border border-green-500/20',
        'batal' => 'bg-red-500/10 text-red-400 border border-red-500/20',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'px-2 py-1 text-xs font-medium rounded-full ' . ($statusColors[$status] ?? 'bg-gray-500/10 text-gray-400 border border-gray-500/20')]) }}>
    {{ strtoupper(str_replace('_', ' ', $status)) }}
</span>
