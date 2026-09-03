@props(['items'])

<nav class="flex items-center gap-1.5 text-[13px] text-ink-mute">
    @foreach ($items as $i => $item)
        <span class="flex items-center gap-1.5">
            @if ($i > 0)<span class="text-line">/</span>@endif
            <span class="{{ $i === count($items) - 1 ? 'text-ink font-medium' : '' }}">{{ $item }}</span>
        </span>
    @endforeach
</nav>
