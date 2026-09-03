@props(['type' => 'text'])

<input type="{{ $type }}" {{ $attributes->merge([
    'class' => 'w-full h-9 px-3 rounded-lg border border-line bg-surface text-sm text-ink placeholder:text-ink-mute focus-ring focus:border-brand-500 transition-colors',
]) }}>
