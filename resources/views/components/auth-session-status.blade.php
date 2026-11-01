@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm']) }} style="color:#00BF6F;background:rgba(0,191,111,0.06);border:1px solid rgba(0,191,111,0.12);border-radius:10px;padding:0.75rem 1rem">
        {{ $status }}
    </div>
@endif