@props(['color' => 'ocre'])

<span {{ $attributes->merge(['class' => 'fb-badge fb-badge-' . $color]) }}>{{ $slot }}</span>
