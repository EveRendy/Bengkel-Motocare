@props(['name'])

@error($name)
    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
@enderror
