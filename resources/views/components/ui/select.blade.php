@props([
    'label' => null,
    'name',
    'options' => [],
    'error' => null,
    'required' => false,
    'placeholder' => 'Pilih...',
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select 
        name="{{ $name }}"
        id="{{ $attributes->get('id') ?? $name }}"
        {{ $attributes->merge(['class' => 'form-select' . ($error ? ' form-input-error' : '')]) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ old($name) == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>

    @if($error)
        <p class="form-error">{{ $error }}</p>
    @endif
</div>
