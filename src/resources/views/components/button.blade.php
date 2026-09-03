{{-- Button — primary | secondary | ghost | danger, sizes sm | md. --}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-brand-600 text-white hover:bg-brand-700 border border-transparent',
        'secondary' => 'bg-surface text-ink border border-line hover:bg-canvas',
        'ghost' => 'bg-transparent text-ink-soft hover:bg-canvas border border-transparent',
        'danger' => 'bg-danger text-white hover:opacity-90 border border-transparent',
    ];
    $sizes = [
        'sm' => 'h-8 px-3 text-[13px] gap-1.5',
        'md' => 'h-9 px-4 text-sm gap-2',
    ];
    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-lg font-medium transition-colors focus-ring disabled:opacity-50 disabled:pointer-events-none',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
