@props(['right' => false])

<th {{ $attributes->merge([
    'class' => 'sticky top-0 z-10 bg-canvas/95 backdrop-blur px-3 py-2.5 text-[12px] font-semibold uppercase tracking-wide text-ink-mute border-b border-line '
        . ($right ? 'text-right' : 'text-left'),
]) }}>{{ $slot }}</th>
