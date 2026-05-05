@props([
    'variant' => 'primary',
    'size' => 'base',
    'href' => null,
    'type' => 'button',
    'block' => false,
])

@php
    $classes = 'fb-btn fb-btn-' . $variant;
    if ($size !== 'base') $classes .= ' fb-btn-' . $size;
    if ($block) $classes .= ' fb-btn-block';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
