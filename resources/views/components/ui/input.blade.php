@props([
    'label' => null,
    'name',
    'id' => null,
    'type' => 'text',
    'error' => null,
    'helper' => null,
    'required' => false,
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        {{ $attributes->merge(['class' => 'form-input' . ($error ? ' form-input-error' : '')]) }}
    >

    @if($helper && !$error)
        <p class="form-helper">{{ $helper }}</p>
    @endif

    @if($error)
        <p class="form-error">{{ $error }}</p>
    @endif
</div>
