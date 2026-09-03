@props(['value'])

@php $clamped = max(0, min(1, (float) $value)); @endphp

<div class="h-1.5 w-full rounded-full bg-surface border border-brand-600 overflow-hidden">
    <div class="h-full rounded-full bg-brand-600" style="width: {{ $clamped * 100 }}%"></div>
</div>
