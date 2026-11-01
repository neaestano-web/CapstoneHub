@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-lg shadow-sm']) }} style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);color:#F1F7F5;padding:0.75rem 1rem;font-size:0.9rem;font-family:inherit;outline:none;transition:all 0.3s ease">