{{-- Badge — brand | info | warn | danger | neutral. --}}
@props(['tone' => 'neutral'])

@php
    $tones = [
        'brand' => 'bg-surface text-brand-700 border-brand-600',
        'info' => 'bg-surface text-info border-info',
        'warn' => 'bg-warn-bg text-warn border-warn-bg',
        'danger' => 'bg-surface text-danger border-danger',
        'neutral' => 'bg-surface text-brand-700 border-brand-600',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[12px] font-medium whitespace-nowrap '
        . ($tones[$tone] ?? $tones['neutral']),
]) }}>{{ $slot }}</span>
