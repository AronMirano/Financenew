<div class="relative">
    <select {{ $attributes->merge([
        'class' => 'w-full h-9 pl-3 pr-9 rounded-lg border border-line bg-surface text-sm text-ink appearance-none focus-ring focus:border-brand-500 transition-colors',
    ]) }}>
        {{ $slot }}
    </select>
    <x-icon name="chevron-down" class="size-4 text-ink-mute absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
</div>
