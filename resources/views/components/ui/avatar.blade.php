@props(['name' => '?', 'size' => '32'])

@php
    $initials = collect(explode(' ', trim($name)))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
    if (!$initials) $initials = '?';
@endphp

<div class="ea-avatar" style="width:{{ $size }}px;height:{{ $size }}px;font-size:{{ round($size * 0.4) }}px;">{{ $initials }}</div>
