{{-- Maps a status string to a badge with a dot (never color-only). --}}
@props(['status'])

@php
    $map = [
        'Draft' => 'neutral',
        'Certified' => 'info',
        'Obligated' => 'warn',
        'Utilized' => 'warn',
        'Paid' => 'brand',
    ];
    $tone = $map[$status] ?? 'neutral';
    $dots = [
        'brand' => 'bg-brand-600',
        'info' => 'bg-info',
        'warn' => 'bg-brand-600',
        'danger' => 'bg-danger',
        'neutral' => 'bg-brand-600',
    ];
@endphp

<x-badge :tone="$tone">
    <span class="size-1.5 rounded-full {{ $dots[$tone] }}"></span>
    {{ $status }}
</x-badge>
