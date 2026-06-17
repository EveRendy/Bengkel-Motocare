@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-gray-800 border border-gray-700 rounded-xl p-6']) }}>
    @if($title)
        <h2 class="text-lg font-semibold text-white mb-4 border-b border-gray-700 pb-2">{{ $title }}</h2>
    @endif

    {{ $slot }}
</div>
