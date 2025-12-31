@props([
    'padding' => true,
    'hover' => true,
])

<div {{ $attributes->merge(['class' => 'card' . ($hover ? '' : ' hover:shadow-card hover:transform-none') . ($padding ? '' : ' p-0')]) }}>
    {{ $slot }}
</div>
