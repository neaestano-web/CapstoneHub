@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm mb-1.5']) }} style="color:rgba(255,255,255,0.65)">
    {{ $value ?? $slot }}
</label>