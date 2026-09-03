@props(['label', 'value', 'hint' => null, 'accent' => false, 'icon' => null])

<x-card class="p-5">
    <div class="flex items-center justify-between">
        <span class="text-[13px] font-medium text-ink-mute">{{ $label }}</span>
        @if ($icon)
            <span class="shrink-0 {{ $accent ? 'text-brand-600' : 'text-ink-mute' }}">
                <x-icon :name="$icon" class="size-4" />
            </span>
        @endif
    </div>
    <div class="mt-2 text-2xl font-semibold tnum {{ $accent ? 'text-brand-700' : 'text-ink' }}">{{ $value }}</div>
    @if ($hint)
        <div class="mt-1 text-[12px] text-ink-mute">{{ $hint }}</div>
    @endif
</x-card>
