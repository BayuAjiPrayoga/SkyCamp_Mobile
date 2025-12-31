@props([
    'label' => null,
    'name',
    'id' => null,
    'rows' => 3,
    'error' => null,
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

    <textarea 
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'form-input resize-none' . ($error ? ' form-input-error' : '')]) }}
    >{{ $slot }}</textarea>

    @if($error)
        <p class="form-error">{{ $error }}</p>
    @endif
</div>
