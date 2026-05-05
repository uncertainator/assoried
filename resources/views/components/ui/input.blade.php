@props([
    'label' => null,
    'name',
    'type' => 'text',
    'help' => null,
    'placeholder' => null,
])

<div class="fb-field">
    @if ($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        class="fb-input {{ $errors->has($name) ? 'is-invalid' : '' }}"
        value="{{ old($name) }}"
        {{ $attributes->except(['label', 'name', 'type', 'help', 'placeholder']) }}
    >
    @error($name)
        <span class="fb-error">{{ $message }}</span>
    @enderror
    @if ($help)
        <span class="fb-help">{{ $help }}</span>
    @endif
</div>
