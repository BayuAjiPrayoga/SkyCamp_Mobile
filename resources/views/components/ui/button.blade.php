@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $classes = 'btn btn-' . $variant;
    if ($size === 'sm')
        $classes .= ' btn-sm';
    if ($size === 'lg')
        $classes .= ' btn-lg';
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
            {{ $slot }}
        </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
        {{ $slot }}
    </button>
@endif
