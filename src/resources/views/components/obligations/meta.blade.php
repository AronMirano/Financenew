@props(['label'])

<div class="flex items-baseline justify-between gap-4 py-1 border-b border-line-soft">
    <span class="text-ink-mute">{{ $label }}</span>
    <span class="text-ink text-right">{{ $slot }}</span>
</div>
