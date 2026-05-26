@props([
    'label' => 'Image',
    'ratio' => '16:9',
])

@php
    $paddingMap = ['16:9' => '56.25%', '1:1' => '100%', '3:4' => '133.33%'];
    $pb = $paddingMap[$ratio] ?? '56.25%';
@endphp

<div {{ $attributes->merge(['style' => "position:relative;padding-bottom:{$pb};background:var(--creme-100);border:1.5px dashed var(--border-default);border-radius:var(--radius-md);overflow:hidden;"]) }}>
    <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                 font:400 var(--text-sm)/1.4 var(--font-sans);color:var(--fg-tertiary);
                 letter-spacing:.04em;text-align:center;padding:8px;">
        {{ $label }}
    </span>
</div>
