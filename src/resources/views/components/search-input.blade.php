{{-- Search field. Submits its enclosing GET form. --}}
@props(['name' => 'q', 'value' => '', 'placeholder' => 'Search…'])

<div class="relative">
    <x-icon name="search" class="size-4 text-ink-mute absolute left-3 top-1/2 -translate-y-1/2" />
    <input
        type="search"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full h-9 pl-9 pr-3 rounded-lg border border-line bg-surface text-sm text-ink placeholder:text-ink-mute focus-ring focus:border-brand-500 transition-colors',
        ]) }}>
</div>
