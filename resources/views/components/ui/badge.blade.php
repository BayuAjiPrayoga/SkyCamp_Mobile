@props([
    'variant' => 'neutral'
])

@php
    $classes = 'badge badge-' . $variant;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
